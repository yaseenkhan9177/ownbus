<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Vendor;
use App\Models\VendorBill;
use Exception;
use Illuminate\Support\Facades\Auth;

class VendorService
{
    protected AccountingService $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    /**
     * Create a vendor with all validation.
     */
    public function createVendor(array $data): Vendor
    {
        return Vendor::create($data);
    }

    /**
     * Update a vendor.
     */
    public function updateVendor(Vendor $vendor, array $data): Vendor
    {
        $vendor->update($data);
        return $vendor->fresh();
    }

    /**
     * Suspend a vendor.
     * Enterprise rule: suspend instead of delete.
     */
    public function suspend(Vendor $vendor): void
    {
        $vendor->update(['status' => Vendor::STATUS_SUSPENDED]);
    }

    /**
     * Reactivate a suspended vendor.
     */
    public function activate(Vendor $vendor): void
    {
        $vendor->update(['status' => Vendor::STATUS_ACTIVE]);
    }

    /**
     * Check if a vendor can be deleted (soft).
     * Enterprise rule: cannot delete if bills or journal entries exist.
     */
    public function canDelete(Vendor $vendor): bool
    {
        if ($vendor->bills()->withTrashed()->exists()) {
            return false;
        }

        // Check journal entries referencing this vendor's bills
        $billIds = $vendor->bills()->pluck('id');
        if (
            $billIds->isNotEmpty() &&
            JournalEntry::whereIn('reference_id', $billIds)
            ->where('reference_type', VendorBill::class)
            ->exists()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Create opening balance journal entry.
     *
     * If payable:
     *   DR Opening Balance Equity (3999)
     *   CR Accounts Payable (2011)
     *
     * If receivable:
     *   DR Accounts Receivable (1013)
     *   CR Opening Balance Equity (3999)
     */
    public function createOpeningBalanceEntry(Vendor $vendor): JournalEntry
    {
        $amount = (float) $vendor->opening_balance;

        $equityAccount = $this->getAccount('3999');  // Opening Balance Equity
        $apAccount     = $this->getAccount('2011');  // Accounts Payable
        $arAccount     = $this->getAccount('1013');  // Accounts Receivable

        if ($vendor->balance_direction === Vendor::DIRECTION_PAYABLE) {
            // DR Equity / CR AP
            $lines = [
                ['account_id' => $equityAccount->id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $apAccount->id,     'debit' => 0,       'credit' => $amount],
            ];
        } else {
            // DR AR / CR Equity
            $lines = [
                ['account_id' => $arAccount->id,    'debit' => $amount, 'credit' => 0],
                ['account_id' => $equityAccount->id, 'debit' => 0,       'credit' => $amount],
            ];
        }

        return $this->accounting->createJournalEntry([
            'branch_id'      => $vendor->branch_id,
            'date'           => now()->toDateString(),
            'description'    => "Opening Balance — Vendor: {$vendor->name} ({$vendor->vendor_code})",
            'reference_type' => Vendor::class,
            'reference_id'   => $vendor->id,
            'is_posted'      => true,
            'created_by'     => Auth::id() ?? $vendor->created_by,
        ], $lines);
    }

    /**
     * Helper: get system account or throw.
     */
    protected function getAccount(string $code): Account
    {
        $account = Account::where('account_code', $code)
            ->first();

        if (!$account) {
            throw new Exception(
                "System account {$code} not found. Please verify Chart of Accounts."
            );
        }

        return $account;
    }
}
