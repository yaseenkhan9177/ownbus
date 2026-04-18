<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountingPeriod;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Rental;
use App\Models\MaintenanceRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AccountingService
{
    /**
     * Create a new journal entry with lines.
     * Rule: DR = CR always.
     */
    public function createJournalEntry(array $data, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($data, $lines) {
            // 1. Validate DR = CR
            $totalDebit = collect($lines)->sum('debit');
            $totalCredit = collect($lines)->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.001) {
                throw new Exception("Accounting mismatch: Total Debit ({$totalDebit}) does not equal Total Credit ({$totalCredit}).");
            }

            // 2. Validate Fiscal Period
            $date = $data['date'] ?? now()->format('Y-m-d');
            $this->validateFiscalPeriod($date);

            // 3. Create Header (JournalEntry)
            $journal = JournalEntry::create(array_merge($data, [
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'created_by' => Auth::id(),
                'is_posted' => $data['is_posted'] ?? true, // Auto-post by default for system entries
                'posted_at' => ($data['is_posted'] ?? true) ? now() : null,
            ]));

            // 4. Create Lines
            foreach ($lines as $line) {
                $account = Account::findOrFail($line['account_id']);

                // Enterprise Rule: Can only post to leaf accounts
                if (!$account->isLeaf()) {
                    throw new Exception("Cannot post to parent account: {$account->account_name}. Please select a sub-account.");
                }

                $journal->lines()->create($line);
            }

            return $journal;
        });
    }

    /**
     * Record Rental Activation (Revenue Recognition)
     * DR Accounts Receivable / CR Rental Income
     */
    public function recordRentalActivation(Rental $rental): JournalEntry
    {
        $arAccount = $this->getSystemAccount('1013'); // Accounts Receivable
        $incomeAccount = $this->getSystemAccount('4010'); // Rental Income

        $amount = (float) $rental->final_amount;

        return $this->createJournalEntry([
            'branch_id' => $rental->branch_id,
            'vehicle_id' => $rental->vehicle_id,
            'date' => now()->toDateString(),
            'description' => "Rental Activation #{$rental->rental_number} - Customer: {$rental->customer->name}",
            'reference_type' => 'App\Models\Rental',
            'reference_id' => $rental->id,
        ], [
            ['account_id' => $arAccount->id, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $incomeAccount->id, 'debit' => 0, 'credit' => $amount],
        ]);
    }

    /**
     * Record Payment Received
     * DR Cash/Bank / CR Accounts Receivable
     */
    public function recordPaymentReceived(Rental $rental, float $amount, string $paymentMethod = 'cash'): JournalEntry
    {
        $cashAccount = ($paymentMethod === 'bank')
            ? $this->getSystemAccount('1012') // Bank
            : $this->getSystemAccount('1011'); // Cash

        $arAccount = $this->getSystemAccount('1013'); // Accounts Receivable

        return $this->createJournalEntry([
            'branch_id' => $rental->branch_id,
            'vehicle_id' => $rental->vehicle_id,
            'date' => now()->toDateString(),
            'description' => "Payment for Rental #{$rental->rental_number} - Method: {$paymentMethod}",
            'reference_type' => 'App\Models\Rental',
            'reference_id' => $rental->id,
        ], [
            ['account_id' => $cashAccount->id, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $arAccount->id, 'debit' => 0, 'credit' => $amount],
        ]);
    }

    /**
     * Record Maintenance Cost
     * DR Maintenance Expense / CR Accounts Payable (or Cash)
     */
    public function recordMaintenanceCost(MaintenanceRecord $record): JournalEntry
    {
        $expenseAccount = $this->getSystemAccount('5012'); // Maintenance
        $apAccount = $this->getSystemAccount('2011'); // Accounts Payable

        $amount = (float) $record->total_cost;

        return $this->createJournalEntry([
            'branch_id' => $record->branch_id,
            'vehicle_id' => $record->vehicle_id,
            'date' => now()->toDateString(),
            'description' => "Maintenance Cost #{$record->maintenance_number} - Vehicle: {$record->vehicle->plate_number}",
            'reference_type' => 'App\Models\MaintenanceRecord',
            'reference_id' => $record->id,
        ], [
            ['account_id' => $expenseAccount->id, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $apAccount->id, 'debit' => 0, 'credit' => $amount],
        ]);
    }

    /**
     * Reverse a journal entry.
     * Enterprise Rule: Never edit; only reverse.
     */
    public function reverseEntry(JournalEntry $journal, string $reason): JournalEntry
    {
        if (!$journal->is_posted) {
            throw new Exception("Cannot reverse an unposted journal entry.");
        }

        if ($journal->reversed_by) {
            throw new Exception("Journal entry #{$journal->id} is already reversed by #{$journal->reversed_by}.");
        }

        return DB::transaction(function () use ($journal, $reason) {
            $reversedLines = $journal->lines->map(function ($line) {
                return [
                    'account_id' => $line->account_id,
                    'debit' => $line->credit, // Swap DR/CR
                    'credit' => $line->debit,
                    'description' => "Reversal of line #{$line->id}"
                ];
            })->toArray();

            $reversal = $this->createJournalEntry([
                'branch_id' => $journal->branch_id,
                'vehicle_id' => $journal->vehicle_id,
                'date' => now()->toDateString(),
                'description' => "REVERSAL of Journal #{$journal->id} - Reason: {$reason}",
                'reference_type' => $journal->reference_type,
                'reference_id' => $journal->reference_id,
                'reversal_of' => $journal->id,
            ], $reversedLines);

            // Link original to reversal
            $journal->update(['reversed_by' => $reversal->id]);

            return $reversal;
        });
    }

    /**
     * Check if a period is open for a given date.
     */
    public function isOpen(string $date): bool
    {
        $period = AccountingPeriod::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        // Rule: If period exists, it must not be closed.
        // If no period exists, we allow posting (flexible SaaS setup).
        return !($period && $period->is_closed);
    }

    /**
     * Validate if the company allows posting for this date.
     */
    protected function validateFiscalPeriod(string $date)
    {
        if (!$this->isOpen($date)) {
            $period = AccountingPeriod::where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->first();

            throw new Exception("Cannot post to a closed accounting period: " . ($period->name ?? $date) . ".");
        }
    }

    /**
     * Helper to get system accounts by code.
     */
    protected function getSystemAccount(string $code): Account
    {
        $account = Account::where('account_code', $code)
            ->first();

        if (!$account) {
            throw new Exception("System account with code {$code} not found. Please verify Chart of Accounts seeder.");
        }

        return $account;
    }
}
