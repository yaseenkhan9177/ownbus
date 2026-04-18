<?php

namespace App\Observers;

use App\Models\FuelLog;
use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class FuelLogObserver
{
    /**
     * Handle the FuelLog "created" event.
     * Enterprise Rule: Every fuel log must hit the ledger.
     */
    public function created(FuelLog $fuelLog): void
    {
        $accounting = app(AccountingService::class);

        DB::transaction(function () use ($fuelLog, $accounting) {
            // 1. Find Accounts
            $fuelExpense = Account::where('account_code', '5011')
                ->first();

            $paymentAccount = $fuelLog->vendor_id
                ? Account::where('account_code', '2011')->first() // AP
                : Account::where('account_code', '1011')->first(); // Cash

            if (!$fuelExpense || !$paymentAccount) {
                throw new Exception("Fuel or Payment accounts not found.");
            }

            // 2. Create Journal Entry
            $accounting->createJournalEntry([
                'company_id' => Auth::user()?->company_id,
                'branch_id' => $fuelLog->branch_id,
                'vehicle_id' => $fuelLog->vehicle_id,
                'date' => $fuelLog->date->toDateString(),
                'description' => "Fuel Entry: Vehicle #{$fuelLog->vehicle_id} - {$fuelLog->liters}L",
                'reference_type' => FuelLog::class,
                'reference_id' => $fuelLog->id,
                'is_posted' => true,
            ], [
                // DR Fuel Expense
                ['account_id' => $fuelExpense->id, 'debit' => $fuelLog->total_amount, 'credit' => 0],
                // CR Cash/AP
                ['account_id' => $paymentAccount->id, 'debit' => 0, 'credit' => $fuelLog->total_amount],
            ]);
        });
    }
}
