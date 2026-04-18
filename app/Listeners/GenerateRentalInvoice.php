<?php

namespace App\Listeners;

use App\Events\RentalCompleted;
use App\Models\Account;
use App\Models\FinancialTransaction;
use App\Models\JournalEntry;
use App\Services\RentalPriceCalculator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class GenerateRentalInvoice implements ShouldQueue
{
    use InteractsWithQueue;

    protected $calculator;

    /**
     * Create the event listener.
     */
    public function __construct(RentalPriceCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Handle the event.
     */
    public function handle(RentalCompleted $event): void
    {
        $rental = $event->rental;

        if ($rental->payment_status === 'paid') {
            return; // Already processed
        }

        DB::transaction(function () use ($rental) {
            // 1. Calculate Final Bill
            $bill = $this->calculator->calculate($rental);

            // 2. Update Rental Financials
            $rental->update([
                'total_amount' => $bill->subtotal,
                'tax_amount' => $bill->tax,
                'final_amount' => $bill->final_amount,
                'payment_status' => 'pending_payment', // Ready for invoicing
                'pricing_adjustments' => array_map(fn($adj) => $adj->toArray(), $bill->adjustments),
            ]);

            // 3. Create Financial Transaction (Accrual Basis: Dr AR, Cr Income)
            $transaction = FinancialTransaction::create([
                'company_id' => $rental->company_id,
                'reference_type' => 'App\Models\Rental',
                'reference_id' => $rental->id,
                'transaction_date' => now(),
                'description' => "Rental Invoice for Contract #{$rental->contract_number}",
            ]);

            // 4. Find Accounts (Using codes from Seeder or System flags)
            // Ideally we'd have a SettingsService to get these IDs.
            // Fallback to strict codes for Phase 1/2.
            $arAccount = Account::withoutGlobalScope('company')->where('company_id', $rental->company_id)->where('account_code', '1013')->first(); // Accounts Receivable
            $incomeAccount = Account::withoutGlobalScope('company')->where('company_id', $rental->company_id)->where('account_code', '4010')->first(); // Rental Income
            $vatAccount = Account::withoutGlobalScope('company')->where('company_id', $rental->company_id)->where('account_code', '2013')->first(); // VAT Payable

            if ($arAccount && $incomeAccount && $vatAccount) {
                // Debit AR (Total)
                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $arAccount->id,
                    'debit' => $bill->final_amount,
                    'credit' => 0,
                ]);

                // Credit Income (Subtotal)
                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $incomeAccount->id,
                    'debit' => 0,
                    'credit' => $bill->subtotal,
                ]);

                // Credit VAT (Tax)
                if ($bill->tax > 0) {
                    JournalEntry::create([
                        'transaction_id' => $transaction->id,
                        'account_id' => $vatAccount->id,
                        'debit' => 0,
                        'credit' => $bill->tax,
                    ]);
                }
            } else {
                // Log warning: Accounts not found
                // For MVP, silence or throw?
                // Throwing might fail the queue job, which is good for retry.
                // throw new \Exception("Default accounts not found for Company {$rental->company_id}");
            }
        });
    }
}
