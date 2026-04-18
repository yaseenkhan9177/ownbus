<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Vendor;
use App\Models\VendorBill;
use App\Models\VendorBillItem;
use App\Services\AccountingService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorBillService
{
    protected AccountingService $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  DRAFT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Create a bill in Draft status with its line items.
     */
    public function createDraft(array $data, array $items): VendorBill
    {
        return DB::transaction(function () use ($data, $items) {
            $bill = VendorBill::create(array_merge($data, [
                'status'     => VendorBill::STATUS_DRAFT,
                'created_by' => Auth::id(),
            ]));

            foreach ($items as $item) {
                $bill->items()->create($item); // total_cost auto-computed in model
            }

            $this->recalculateTotals($bill);

            return $bill->fresh(['items', 'vendor']);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  APPROVE — Creates Journal Entry
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Approve a draft bill and post the accounting journal.
     *
     * DR → each Expense Account (from bill items)
     * CR → Accounts Payable (2011)
     */
    public function approveBill(VendorBill $bill): JournalEntry
    {
        return DB::transaction(function () use ($bill) {

            // ── Guard: Must be draft ──────────────────────────────────────────
            if (!$bill->canBeApproved()) {
                throw new Exception("Bill #{$bill->bill_number} cannot be approved. Current status: {$bill->status}.");
            }

            // ── Guard: Vendor must be active ──────────────────────────────────
            if ($bill->vendor->isSuspended()) {
                throw new Exception("Cannot approve bill for suspended vendor: {$bill->vendor->name}.");
            }

            // ── Guard: Must have items ────────────────────────────────────────
            $bill->load('items.expenseAccount');

            if ($bill->items->isEmpty()) {
                throw new Exception("Cannot approve bill with no line items.");
            }

            // ── Guard: Total must be > 0 ──────────────────────────────────────
            $this->recalculateTotals($bill);
            $bill->refresh();

            if ((float) $bill->total_amount <= 0) {
                throw new Exception("Cannot approve bill with zero total amount.");
            }

            // ── Guard: Each expense account must be a leaf expense account ────
            foreach ($bill->items as $item) {
                $account = $item->expenseAccount;
                if (!$account) {
                    throw new Exception("Item '{$item->description}' has no expense account.");
                }
                if (!$account->isLeaf()) {
                    throw new Exception(
                        "Account '{$account->account_name}' is a parent account. Please select a sub-account."
                    );
                }
                if ($account->account_type !== 'expense') {
                    throw new Exception(
                        "Account '{$account->account_name}' is not an expense account (type: {$account->account_type})."
                    );
                }
            }

            // ── Build journal lines ───────────────────────────────────────────
            $apAccount = $this->getAccount('2011');
            $lines     = [];

            foreach ($bill->items as $item) {
                $lines[] = [
                    'account_id' => $item->expense_account_id,
                    'debit'      => (float) $item->total_cost,
                    'credit'     => 0,
                ];
            }

            // CR Accounts Payable = total bill
            $lines[] = [
                'account_id' => $apAccount->id,
                'debit'      => 0,
                'credit'     => (float) $bill->total_amount,
            ];

            // ── Post journal ──────────────────────────────────────────────────
            $journal = $this->accounting->createJournalEntry([
                'branch_id'      => $bill->branch_id,
                'date'           => $bill->bill_date->toDateString(),
                'description'    => "Bill Approved — #{$bill->bill_number} — Vendor: {$bill->vendor->name}",
                'reference_type' => VendorBill::class,
                'reference_id'   => $bill->id,
                'is_posted'      => true,
                'created_by'     => Auth::id(),
            ], $lines);

            // ── Update bill status ────────────────────────────────────────────
            $bill->update([
                'status'      => VendorBill::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return $journal;
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PAYMENT — DR Accounts Payable / CR Cash or Bank
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Record a payment against an approved bill.
     *
     * DR → Accounts Payable (2011)
     * CR → Cash (1011) or Bank (1012)
     *
     * Automatically updates status to partially_paid or paid.
     */
    public function recordPayment(VendorBill $bill, float $amount, string $method = 'cash'): JournalEntry
    {
        return DB::transaction(function () use ($bill, $amount, $method) {

            // ── Guard: must be payable ────────────────────────────────────────
            if (!$bill->canBePaid()) {
                throw new Exception("Bill #{$bill->bill_number} cannot be paid. Status: {$bill->status}.");
            }

            $remaining = $bill->remainingAmount();

            if ($amount <= 0) {
                throw new Exception("Payment amount must be greater than zero.");
            }

            if ($amount > $remaining + 0.001) {
                throw new Exception(
                    "Payment amount ({$amount}) exceeds remaining balance ({$remaining})."
                );
            }

            // ── Accounts ──────────────────────────────────────────────────────
            $apAccount   = $this->getAccount('2011');
            $cashAccount = ($method === 'bank')
                ? $this->getAccount('1012')
                : $this->getAccount('1011');

            // ── Post journal ──────────────────────────────────────────────────
            $journal = $this->accounting->createJournalEntry([
                'branch_id'      => $bill->branch_id,
                'date'           => now()->toDateString(),
                'description'    => "Payment — Bill #{$bill->bill_number} — Vendor: {$bill->vendor->name} — Method: {$method}",
                'reference_type' => VendorBill::class,
                'reference_id'   => $bill->id,
                'is_posted'      => true,
                'created_by'     => Auth::id(),
            ], [
                ['account_id' => $apAccount->id,   'debit' => $amount, 'credit' => 0],
                ['account_id' => $cashAccount->id,  'debit' => 0,       'credit' => $amount],
            ]);

            // ── Update bill status ────────────────────────────────────────────
            $newPaid    = $bill->paidAmount() + $amount;
            $newStatus  = ($newPaid >= (float) $bill->total_amount - 0.001)
                ? VendorBill::STATUS_PAID
                : VendorBill::STATUS_PARTIALLY_PAID;

            $bill->update(['status' => $newStatus]);

            return $journal;
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  CANCEL — Reversal Entry if Approved
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Cancel a bill. If it was approved, create a reversal journal entry.
     * Enterprise rule: never silent editing — always trace.
     */
    public function cancel(VendorBill $bill, string $reason = 'Cancelled by user'): void
    {
        DB::transaction(function () use ($bill, $reason) {

            if (!$bill->canBeCancelled()) {
                throw new Exception("Bill #{$bill->bill_number} cannot be cancelled. Status: {$bill->status}.");
            }

            // If already approved (or partially paid), reverse the approval entry
            if (!$bill->isDraft()) {
                $approvalEntry = $bill->journalEntries()
                    ->where('description', 'like', 'Bill Approved%')
                    ->first();

                if ($approvalEntry) {
                    $this->accounting->reverseEntry($approvalEntry, "Bill Cancelled — #{$bill->bill_number} — {$reason}");
                }
            }

            $bill->update(['status' => VendorBill::STATUS_CANCELLED]);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Recalculate bill total from items and tax.
     * Enterprise rule: total must always be computed, never manually set.
     */
    public function recalculateTotals(VendorBill $bill): void
    {
        $bill->load('items');
        $itemsTotal = $bill->items->sum(fn($item) => (float) $item->total_cost);
        $tax        = (float) ($bill->tax_amount ?? 0);

        $bill->update(['total_amount' => round($itemsTotal + $tax, 2)]);
    }

    /**
     * Delete a bill (Soft Delete).
     * Enterprise rule: Cannot delete approved or paid bills.
     */
    public function deleteBill(VendorBill $bill): void
    {
        if ($bill->status !== VendorBill::STATUS_DRAFT && $bill->status !== VendorBill::STATUS_CANCELLED) {
            throw new Exception("Cannot delete bill #{$bill->bill_number} in current status: {$bill->status}. Only Draft or Cancelled bills can be deleted.");
        }

        $bill->delete();
    }

    protected function getAccount(string $code): Account
    {
        $account = Account::where('account_code', $code)
            ->first();

        if (!$account) {
            throw new Exception("System account {$code} not found. Verify Chart of Accounts.");
        }

        return $account;
    }
}
