<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Account;
use App\Models\Company;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class ExpenseService
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Create a new expense and its corresponding journal entry.
     */
    public function createExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Expense Record
            $expense = Expense::create(array_merge($data, [
                'created_by' => Auth::id(),
            ]));

            // 2. Auto Journal Entry
            $this->createAccountingEntry($expense);

            return $expense;
        });
    }

    /**
     * Generate the accounting journal entry for an expense.
     * Dr Expense Account
     * Dr VAT Input (if applicable)
     * Cr Cash / Bank / Payable
     */
    protected function createAccountingEntry(Expense $expense)
    {
        $companyId = $expense->company_id;

        // Determine Expense Account based on category
        $expenseAccountCode = $this->getAccountCodeByCategory($expense->category);
        $expenseAccount = Account::where('account_code', $expenseAccountCode)
            ->first();

        if (!$expenseAccount) {
            // Dynamically create the missing leaf account under Operating Expenses (5010)
            $parentAccount = Account::where('account_code', '5010')
                ->first();

            $accountName = match ($expenseAccountCode) {
                '5020' => 'Payroll / Salaries',
                '5030' => 'Rent Expense',
                '5040' => 'Utilities',
                '5050' => 'Insurance',
                '5060' => 'Marketing',
                default => 'Other Operating Expenses', // e.g. 5019
            };

            $expenseAccount = Account::create([
                'parent_id' => $parentAccount ? $parentAccount->id : null,
                'account_code' => $expenseAccountCode,
                'account_name' => $accountName,
                'account_type' => 'expense',
                'is_system' => false,
                'is_active' => true,
            ]);
        }

        // Determine Credit Account (Payment Method)
        $creditAccountCode = match ($expense->payment_method) {
            'bank'   => '1012', // Bank
            'cash'   => '1011', // Cash
            'payable' => '2011', // Accounts Payable
            default   => '1011',
        };
        $creditAccount = Account::where('account_code', $creditAccountCode)
            ->first();

        if (!$expenseAccount || !$creditAccount) {
            throw new Exception("Accounting failure: Required accounts for category '{$expense->category}' or payment method '{$expense->payment_method}' not found.");
        }

        $lines = [];

        // Line 1: Dr Expense (Amount without VAT)
        $lines[] = [
            'account_id' => $expenseAccount->id,
            'debit' => (float) $expense->amount_ex_vat,
            'credit' => 0,
            'description' => "Expense: {$expense->category} - {$expense->description}",
        ];

        // Line 2: Dr VAT Input (if applicable)
        if ($expense->vat_amount > 0) {
            $vatAccount = Account::where('account_code', '2013') // VAT Payable/Input
                ->first();

            if ($vatAccount) {
                $lines[] = [
                    'account_id' => $vatAccount->id,
                    'debit' => (float) $expense->vat_amount,
                    'credit' => 0,
                    'description' => "VAT Input for {$expense->category}",
                ];
            } else {
                // If no VAT account, add VAT to expense amount (capitalize it)
                $lines[0]['debit'] += (float) $expense->vat_amount;
            }
        }

        // Line 3: Cr Payment Method (Total Amount)
        $lines[] = [
            'account_id' => $creditAccount->id,
            'debit' => 0,
            'credit' => (float) $expense->total_amount,
            'description' => "Payment for {$expense->category} via {$expense->payment_method}",
        ];

        // Create Journal Entry via AccountingService
        $this->accountingService->createJournalEntry([
            'branch_id' => $expense->branch_id,
            'vehicle_id' => $expense->vehicle_id,
            'date' => $expense->expense_date->toDateString(),
            'description' => "Expense Entry: {$expense->category} - {$expense->description}",
            'reference_type' => 'App\Models\Expense',
            'reference_id' => $expense->id,
        ], $lines);
    }

    /**
     * Map category to account code.
     */
    protected function getAccountCodeByCategory(string $category): string
    {
        return match ($category) {
            'fuel'        => '5011',
            'maintenance', 'repair', 'parts' => '5012',
            'salik'       => '5013',
            'fines'       => '5014',
            'salary', 'salaries' => '5020',
            'rent'        => '5030',
            'utilities'   => '5040',
            'insurance'   => '5050',
            'marketing'   => '5060',
            default       => '5019', // Other Operating Expenses (Leaf Account)
        };
    }
}
