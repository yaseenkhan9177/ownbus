<?php

namespace App\Services;

use App\Models\VehicleFine;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Company;
use App\Services\AccountingService;
use App\Services\ExpenseService;
use App\Traits\LogsEvents;
use App\Services\EventLoggerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class FineService
{
    use LogsEvents;

    protected $accountingService;
    protected $expenseService;

    public function __construct(AccountingService $accountingService, ExpenseService $expenseService)
    {
        $this->accountingService = $accountingService;
        $this->expenseService = $expenseService;
    }

    public function createFine(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Fine Record
            $fine = VehicleFine::create(array_merge($data, [
                'created_by' => Auth::id(),
            ]));

            // 2. Initial Impact
            $this->updateRiskScores($fine);

            // 3. If Paid immediately, handle accounting
            if ($fine->status === 'paid') {
                $this->handleFinePayment($fine);
            }

            return $fine;
        });
    }

    public function updateFineStatus(VehicleFine $fine, string $status, array $extra = [])
    {
        return DB::transaction(function () use ($fine, $status, $extra) {
            $oldStatus = $fine->status;
            $fine->update(array_merge(['status' => $status], $extra));

            if ($status === 'paid' && $oldStatus !== 'paid') {
                $this->handleFinePayment($fine);

                $this->logEvent(
                    Auth::user()->company,
                    EventLoggerService::FINE_PAID,
                    $fine,
                    "Fine #{$fine->fine_number} paid to authority",
                    ['amount' => $fine->amount, 'authority' => $fine->authority]
                );
            }

            return $fine;
        });
    }

    protected function handleFinePayment(VehicleFine $fine)
    {
        // 1. Create Expense Entry
        // Since Fines represent an operational cost if the company pays them.
        // Even if customer is responsible, company usually pays RTA first.

        // Resolve branch_id through a cascade of fallbacks:
        // 1. Fine's own branch_id (set at creation time)
        // 2. Vehicle's branch_id (vehicle may belong to a branch even if admin didn't specify)
        // 3. Company's first/default branch (handles single-branch and admin-created records)
        $branchId = $fine->branch_id
            ?? Vehicle::find($fine->vehicle_id)?->branch_id
            ?? \App\Models\Branch::value('id');

        $expense = $this->expenseService->createExpense([
            'company_id' => $fine->company_id,
            'branch_id' => $branchId,
            'vehicle_id' => $fine->vehicle_id,
            'category' => 'fines',
            'description' => "Traffic Fine #{$fine->fine_number} - Source: {$fine->source}",
            'amount_ex_vat' => $fine->amount, // Fines usually have no VAT
            'vat_percent' => 0,
            'vat_amount' => 0,
            'total_amount' => $fine->amount,
            'expense_date' => $fine->paid_at ?? now(),
            'payment_method' => $fine->payment_reference ? 'bank' : 'cash',
            'reference_no' => $fine->payment_reference,
        ]);

        // 2. Responsibility-based Accounting Recovery
        switch ($fine->responsible_type) {
            case 'driver':
                // Dr. Driver Advances/Receivables (1014)
                // Cr. Fine Income / Recovery (4011)
                $this->accountingService->createJournalEntry([
                    'branch_id' => $branchId,
                    'date' => ($fine->paid_at ?? now())->format('Y-m-d'),
                    'reference' => "FINE-RECOV-" . $fine->fine_number,
                    'description' => "Fine recovery receivable from Driver: " . ($fine->driver->name ?? 'Unknown'),
                ], [
                    ['account_id' => Account::where('account_code', '1014')->value('id'), 'debit' => $fine->amount, 'credit' => 0],
                    ['account_id' => Account::where('account_code', '4011')->value('id'), 'debit' => 0, 'credit' => $fine->amount],
                ]);
                break;
            case 'customer':
                // Dr. Accounts Receivable (1013)
                // Cr. Fine Income / Recovery (4011)
                $this->accountingService->createJournalEntry([
                    'branch_id' => $branchId,
                    'date' => ($fine->paid_at ?? now())->format('Y-m-d'),
                    'reference' => "FINE-RECOV-" . $fine->fine_number,
                    'description' => "Fine recovery receivable from Customer: " . ($fine->customer->name ?? 'Unknown'),
                ], [
                    ['account_id' => Account::where('account_code', '1013')->value('id'), 'debit' => $fine->amount, 'credit' => 0],
                    ['account_id' => Account::where('account_code', '4011')->value('id'), 'debit' => 0, 'credit' => $fine->amount],
                ]);
                break;
            case 'both':
                // Split 50/50 for demonstration, in a real app this might be configurable
                $half = $fine->amount / 2;
                $this->accountingService->createJournalEntry([
                    'branch_id' => $branchId,
                    'date' => ($fine->paid_at ?? now())->format('Y-m-d'),
                    'reference' => "FINE-RECOV-" . $fine->fine_number,
                    'description' => "Shared fine recovery (Driver/Customer)",
                ], [
                    // Dr Driver
                    ['account_id' => Account::where('account_code', '1014')->value('id'), 'debit' => $half, 'credit' => 0],
                    // Dr Customer
                    ['account_id' => Account::where('account_code', '1013')->value('id'), 'debit' => $half, 'credit' => 0],
                    // Cr Income
                    ['account_id' => Account::where('account_code', '4011')->value('id'), 'debit' => 0, 'credit' => $fine->amount],
                ]);
                break;
            case 'company':
            default:
                // No recovery action needed, the expense entry above handles the company's liability
                break;
        }
    }

    protected function updateRiskScores(VehicleFine $fine)
    {
        // Update Driver Risk
        if ($fine->driver_id && $fine->black_points > 0) {
            $driver = Driver::find($fine->driver_id);
            if ($driver) {
                // Logic to increment driver's total black points or recalculate score
                // For now, we assume there's a field or we just trigger score update
                $this->enforceSmartCompliance($driver, $fine);
            }
        }

        // Update Vehicle Risk (High frequency of fines = High Risk)
        if ($fine->vehicle_id) {
            $vehicle = Vehicle::find($fine->vehicle_id);
            // Heuristic update
        }
    }

    protected function enforceSmartCompliance(Driver $driver, VehicleFine $fine)
    {
        // UAE Rule: If black points > limit (e.g. 24), license suspended
        // In our app, we can flag the driver.
        $totalPoints = VehicleFine::where('driver_id', $driver->id)
            ->where('fine_date', '>', now()->subYear())
            ->sum('black_points');

        if ($totalPoints >= 20) {
            // High priority alert
            // Block assignment logic should check this totalPoints
        }
    }
}
