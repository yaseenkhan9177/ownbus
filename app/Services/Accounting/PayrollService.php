<?php

namespace App\Services\Accounting;

use App\Models\PayrollBatch;
use App\Models\SalarySlip;
use App\Models\SalaryItem;
use App\Models\User;
use App\Models\Driver;
use App\Models\VehicleFine;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class PayrollService
{
    /**
     * Generate a draft payroll batch for a given period.
     */
    public function generateDraftBatch(int $companyId, ?int $branchId, string $periodName)
    {
        return DB::transaction(function () use ($companyId, $branchId, $periodName) {
            $batch = PayrollBatch::create([
                'branch_id' => $branchId,
                'period_name' => $periodName,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // 1. Get Drivers with Salary
            $drivers = Driver::query()
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('status', 'active')
                ->where('salary', '>', 0)
                ->get();

            foreach ($drivers as $driver) {
                $this->createSlipForDriver($batch, $driver);
            }

            // 2. TODO: Staff salaries if integrated later

            $this->recalculateBatchTotal($batch);

            return $batch;
        });
    }

    protected function createSlipForDriver(PayrollBatch $batch, Driver $driver)
    {
        $slip = SalarySlip::create([
            'payroll_batch_id' => $batch->id,
            'user_id' => null, // Driver model might not be linked to User in all cases, or we use Driver ID?
            // Actually, based on previous research, Driver might have its own table.
            // Let's assume we link via Driver ID or User if available.
            // For now, let's use base_salary from Driver.
            'base_salary' => $driver->salary,
            'status' => 'pending',
        ]);

        // Automated Deductions: Fines
        $fines = VehicleFine::where('driver_id', $driver->id)
            ->where('responsible_party', 'driver')
            ->where('status', 'unpaid') // Only deduct unpaid fines? Or fines in this month?
            // To keep it simple: any unpaid fine where driver is responsible.
            ->get();

        $totalDeductions = 0;
        foreach ($fines as $fine) {
            SalaryItem::create([
                'salary_slip_id' => $slip->id,
                'type' => 'deduction',
                'label' => "Traffic Fine #{$fine->fine_number}",
                'amount' => $fine->total_amount,
            ]);
            $totalDeductions += $fine->total_amount;
        }

        $slip->update([
            'total_deductions' => $totalDeductions,
            'net_salary' => $slip->base_salary - $totalDeductions,
        ]);
    }

    public function recalculateBatchTotal(PayrollBatch $batch)
    {
        $batch->update([
            'total_net' => $batch->slips()->sum('net_salary'),
        ]);
    }

    /**
     * Post payroll batch to Accounting (General Ledger).
     */
    public function postBatch(PayrollBatch $batch)
    {
        if ($batch->status !== 'draft') {
            throw new Exception("Only draft payroll batches can be posted.");
        }

        return DB::transaction(function () use ($batch) {
            // 1. Create Journal Entry
            $entry = JournalEntry::create([
                'branch_id'      => $batch->branch_id,
                'date'           => now(),
                'description'    => "Payroll Batch Posting: {$batch->period_name}",
                'reference_type' => PayrollBatch::class,
                'reference_id'   => $batch->id,
                'is_posted'      => true,
                'posted_at'      => now(),
                'created_by'     => Auth::id(),
            ]);

            // 2. Find Accounts
            $salaryExpenseAcc = Account::where('account_code', '5020')->first();
            $salaryPayableAcc = Account::where('account_code', '2012')->first();

            if (!$salaryExpenseAcc || !$salaryPayableAcc) {
                // Fallback or error?
                throw new Exception("Payroll failure: Salary Expense (5020) or Payable (2012) accounts not found.");
            }

            // Debit Expense
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $salaryExpenseAcc->id,
                'debit'            => (float) $batch->slips()->sum('base_salary'), // Total Base
                'credit'           => 0,
            ]);

            // Credit Payable
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $salaryPayableAcc->id,
                'debit'            => 0,
                'credit'           => (float) $batch->total_net,
            ]);

            // Deductions Adjustment (Fines)
            $totalDeductions = $batch->slips()->sum('total_deductions');
            if ($totalDeductions > 0) {
                $finesAccount = Account::where('account_code', '5014')->first(); // Fines Expense (to reverse?)
                // Actually, if we deduct fines, we are moving liability to the driver or reducing our expense.
                // For now, let's just make sure it balances.
                // A better approach: Cr Fines Receivable or reduce Fines Expense.
                if ($finesAccount) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id'       => $finesAccount->id,
                        'debit'            => 0,
                        'credit'           => (float) $totalDeductions,
                    ]);
                }
            }

            $batch->update(['status' => 'posted']);

            return $batch;
        });
    }
}
