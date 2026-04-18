<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Company;
use App\Models\VehicleFine;
use Illuminate\Support\Facades\Log;

class FineRecoveryService
{
    protected AccountingService $accounting;

    // Account codes
    const ACCOUNT_AR              = '1013'; // Accounts Receivable
    const ACCOUNT_FINE_RECOVERY   = '4099'; // Fine Recovery Income

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    /**
     * Post a journal entry to recover a fine from the responsible customer.
     * DR Accounts Receivable / CR Fine Recovery Income
     *
     * Safe to call multiple times — idempotent (skips if already linked).
     */
    public function recoverFine(VehicleFine $fine): void
    {
        if (!$fine->customer_responsible) {
            Log::info("FineRecovery: Fine #{$fine->id} is not customer-responsible. Skipping.");
            return;
        }

        if (!$fine->customer_id) {
            Log::warning("FineRecovery: Fine #{$fine->id} has no customer linked. Cannot recover.");
            return;
        }

        if ($fine->journal_entry_id) {
            Log::info("FineRecovery: Fine #{$fine->id} already has journal entry #{$fine->journal_entry_id}. Skipping.");
            return;
        }

        $fine->load('customer', 'vehicle');
        $amount   = (float) $fine->amount;
        $customer = $fine->customer;
        $vehicle  = $fine->vehicle;

        $arAccount  = $this->getAccount(self::ACCOUNT_AR);
        $recAccount = $this->getAccount(self::ACCOUNT_FINE_RECOVERY);

        $journal = $this->accounting->createJournalEntry([
            'date'           => now()->toDateString(),
            'description'    => "Fine Recovery — {$vehicle->vehicle_number} | Fine #{$fine->fine_number} | Customer: {$customer->name}",
            'reference_type' => VehicleFine::class,
            'reference_id'   => $fine->id,
        ], [
            ['account_id' => $arAccount->id,  'debit' => $amount, 'credit' => 0,      'description' => "Fine recovery AR — {$customer->name}"],
            ['account_id' => $recAccount->id, 'debit' => 0,       'credit' => $amount, 'description' => "Fine Recovery Income — {$fine->authority}"],
        ]);

        // Link journal entry back to fine and mark as recovered
        $fine->update([
            'journal_entry_id' => $journal->id,
            'status'           => 'recovered',
        ]);

        // Update customer AR balance
        $customer->increment('current_balance', $amount);

        Log::info("FineRecovery: Posted journal #{$journal->id} for fine #{$fine->id} — AED {$amount} — status: recovered");
    }

    /**
     * Batch-recover all unrecovered customer-responsible fines for a company.
     */
    public function recoverAllPending(Company $company): int
    {
        $recovered = 0;

        VehicleFine::where('customer_responsible', true)
            ->whereNull('journal_entry_id')
            ->whereNotNull('customer_id')
            ->where('status', 'pending')
            ->get()
            ->each(function (VehicleFine $fine) use (&$recovered) {
                try {
                    $this->recoverFine($fine);
                    $recovered++;
                } catch (\Throwable $e) {
                    Log::error("FineRecovery: Failed for fine #{$fine->id} — {$e->getMessage()}");
                }
            });

        return $recovered;
    }

    protected function getAccount(string $code): Account
    {
        $account = Account::where('account_code', $code)->first();
        if (!$account) {
            throw new \RuntimeException("Account code {$code} not found.");
        }
        return $account;
    }
}
