<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\FinancialTransaction;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    /**
     * Get paginated transactions with filters.
     */
    public function getTransactions(Company $company, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = FinancialTransaction::with(['journalEntries.account', 'reference']);

        // Filter by Date Range
        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        // Filter by Reference Type (e.g., Rental, Maintenance)
        if (!empty($filters['reference_type'])) {
            $query->where('reference_type', $filters['reference_type']);
        }

        // Search description
        if (!empty($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get financial summary (Income vs Expenses) for a given period.
     */
    public function getFinancialSummary(Company $company, $startDate, $endDate): array
    {
        // Aggregate Income (Revenue Accounts)
        // Use JournalEntryLine for detail sum
        $income = JournalEntryLine::whereHas('transaction', function ($q) use ($company, $startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })->whereHas('account', function ($q) {
            $q->where('account_type', 'income'); // COA uses 'income'
        })->select(DB::raw('SUM(credit - debit) as total, COUNT(DISTINCT journal_entry_id) as count'))
            ->first();

        // Aggregate Expenses (Expense Accounts)
        $expense = JournalEntryLine::whereHas('transaction', function ($q) use ($company, $startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })->whereHas('account', function ($q) {
            $q->where('account_type', 'expense');
        })->select(DB::raw('SUM(debit - credit) as total, COUNT(DISTINCT journal_entry_id) as count'))
            ->first();

        $totalIncome = (float) ($income->total ?? 0);
        $totalExpense = (float) ($expense->total ?? 0);

        return [
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'net_profit' => $totalIncome - $totalExpense,
            'transaction_count_income' => $income->count ?? 0,
            'transaction_count_expense' => $expense->count ?? 0,
        ];
    }
}
