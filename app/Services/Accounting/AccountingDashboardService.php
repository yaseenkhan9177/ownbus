<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountingDashboardService
{
    /**
     * Get Financial KPIs for the dashboard.
     */
    public function getKpis(int $companyId): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. Total Revenue (This Month) - Income accounts (Credit Normal)
        $revenue = $this->getSumForTypes($companyId, ['income'], $startOfMonth, $endOfMonth);

        // 2. Total Expenses (This Month) - Expense accounts (Debit Normal)
        $expenses = $this->getSumForTypes($companyId, ['expense'], $startOfMonth, $endOfMonth);

        // 3. Outstanding Receivables - Asset accounts with code '1013' or similar (Accounts Receivable)
        $receivables = $this->getAccountBalance($companyId, '1013');

        // 4. Outstanding Payables - Liability accounts with code '2011' or similar (Accounts Payable)
        $payables = $this->getAccountBalance($companyId, '2011');

        // 5. Cash in Bank - Asset accounts with code '1012'
        $bankBalance = $this->getAccountBalance($companyId, '1012');

        // 6. Cash in Hand - Asset accounts with code '1011'
        $cashBalance = $this->getAccountBalance($companyId, '1011');

        return [
            'revenue_this_month' => $revenue,
            'expenses_this_month' => $expenses,
            'net_profit' => $revenue - $expenses,
            'receivables' => $receivables,
            'payables' => $payables,
            'bank_balance' => $bankBalance,
            'cash_balance' => $cashBalance,
            'total_liquidity' => $bankBalance + $cashBalance,
        ];
    }

    /**
     * Get Revenue Trend for the last 6 months.
     */
    public function getRevenueTrend(int $companyId): array
    {
        $months = [];
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $sum = $this->getSumForTypes($companyId, ['income'], $start, $end);

            $labels[] = $date->format('M Y');
            $data[] = $sum;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get Expense Breakdown by category for the current month.
     */
    public function getExpenseBreakdown(int $companyId): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $expenses = DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.is_posted', true)
            ->where('accounts.account_type', 'expense')
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->select('accounts.account_name', DB::raw('SUM(journal_entry_lines.debit - journal_entry_lines.credit) as total'))
            ->groupBy('accounts.account_name')
            ->having('total', '>', 0)
            ->get();

        return [
            'labels' => $expenses->pluck('account_name')->toArray(),
            'data' => $expenses->pluck('total')->map(fn($v) => (float)$v)->toArray(),
        ];
    }

    /**
     * Helper to sum movements for account types.
     */
    protected function getSumForTypes(int $companyId, array $types, Carbon $start, Carbon $end): float
    {
        $totals = DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.is_posted', true)
            ->whereIn('accounts.account_type', $types)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('SUM(journal_entry_lines.debit) as total_debit, SUM(journal_entry_lines.credit) as total_credit')
            ->first();

        $dr = (float)($totals->total_debit ?? 0);
        $cr = (float)($totals->total_credit ?? 0);

        // Account logic: Income/Liability/Equity = CR - DR, Asset/Expense = DR - CR
        if (in_array($types[0], ['income', 'liability', 'equity'])) {
            return $cr - $dr;
        }
        return $dr - $cr;
    }

    /**
     * Helper to get current balance of a specific account code.
     */
    protected function getAccountBalance(int $companyId, string $code): float
    {
        $account = Account::where('account_code', $code)
            ->first();

        if (!$account) return 0;

        $totals = DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $account->id)
            ->where('journal_entries.is_posted', true)
            ->selectRaw('SUM(journal_entry_lines.debit) as total_debit, SUM(journal_entry_lines.credit) as total_credit')
            ->first();

        $dr = (float)($totals->total_debit ?? 0);
        $cr = (float)($totals->total_credit ?? 0);

        if (in_array($account->account_type, ['asset', 'expense'])) {
            return $dr - $cr;
        }
        return $cr - $dr;
    }
}
