<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Enterprise Financial Report Service
 *
 * ALL reports read exclusively from journal_entry_lines joined to journal_entries.
 * Enterprise rules enforced:
 *   - Only POSTED entries (is_posted = true) are included.
 *   - Branch filter applies to journal_entries.branch_id (header), never to lines directly.
 *   - All reports support date ranges and branch isolation.
 */
class FinancialReportService
{
    // =========================================================================
    // TRIAL BALANCE
    // =========================================================================

    /**
     * Generate Trial Balance for a date range.
     *
     * Formula:
     *   Opening Balance = sum(dr-cr) from beginning-of-time BEFORE $startDate
     *   Period Movement = sum(dr-cr) WITHIN $startDate to $endDate
     *   Closing Balance = Opening + Period Movement
     *
     * Why: A mid-year trial balance must show opening balances, not start from zero.
     *
     * @return array  Contains rows, total_debit, total_credit, difference, is_balanced, start_date, end_date
     */
    public function getTrialBalance(int $companyId, Carbon $startDate, Carbon $endDate, ?int $branchId = null): array
    {
        $accounts = Account::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $rows = [];
        $totalDebit  = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            // Opening balance: all posted transactions BEFORE the period start
            $openingBalance = $this->sumAccountLines(
                $account->id,
                Carbon::create(1900, 1, 1),
                $startDate->copy()->subDay(),
                $branchId
            );

            // Period movement: all posted transactions WITHIN the range
            $period = $this->sumAccountLines(
                $account->id,
                $startDate,
                $endDate,
                $branchId,
                true  // return raw dr/cr totals
            );

            $periodDebit  = $period['debit'];
            $periodCredit = $period['credit'];

            // Normalised closing balance applying account normal-balance rule
            $closingNormalised = $this->normalizeBalance(
                $account->account_type,
                $openingBalance + ($periodDebit - $periodCredit)
            );

            // For TB display: place closing balance in Debit or Credit column
            $netDr = $openingBalance + ($periodDebit - $periodCredit);
            $closingDebit  = $netDr > 0 ? $netDr : 0;
            $closingCredit = $netDr < 0 ? abs($netDr) : 0;

            // Only show accounts with activity or a balance
            if ($openingBalance == 0 && $periodDebit == 0 && $periodCredit == 0) {
                continue;
            }

            $rows[] = [
                'account_code'    => $account->account_code,
                'account_name'    => $account->account_name,
                'account_type'    => $account->account_type,
                'opening_balance' => $openingBalance,
                'period_debit'    => $periodDebit,
                'period_credit'   => $periodCredit,
                'closing_debit'   => $closingDebit,
                'closing_credit'  => $closingCredit,
            ];

            $totalDebit  += $closingDebit;
            $totalCredit += $closingCredit;
        }

        $difference = abs($totalDebit - $totalCredit);

        return [
            'rows'         => $rows,
            'total_debit'  => $totalDebit,
            'total_credit' => $totalCredit,
            'difference'   => $difference,
            'is_balanced'  => $difference < 0.01,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
        ];
    }

    // =========================================================================
    // GENERAL LEDGER
    // =========================================================================

    /**
     * General Ledger for a specific account.
     *
     * Returns:
     *   - Opening balance (before period)
     *   - Each journal line sorted by date ASC, then journal_entry_id ASC, then line id ASC
     *   - Running balance after each entry (computed in-memory, not SQL)
     *   - Closing balance
     *
     * Running balance rule:
     *   Debit-normal accounts  (asset, expense): balance += debit - credit
     *   Credit-normal accounts (income, liability, equity): balance += credit - debit
     */
    public function getGeneralLedger(Account $account, Carbon $startDate, Carbon $endDate, ?int $branchId = null): array
    {
        // Opening balance = everything BEFORE the period start (normalised)
        $beforePeriod = $this->sumAccountLines(
            $account->id,
            Carbon::create(1900, 1, 1),
            $startDate->copy()->subDay(),
            $branchId,
            true
        );

        $debitNormal    = $this->isDebitNormal($account->account_type);
        $openingBalance = $debitNormal
            ? ($beforePeriod['debit'] - $beforePeriod['credit'])
            : ($beforePeriod['credit'] - $beforePeriod['debit']);

        // Fetch all lines within the period, ordered for running balance
        $lines = JournalEntryLine::where('account_id', $account->id)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate, $branchId) {
                $q->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->where('is_posted', true);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            })
            ->with([
                'journalEntry:id,date,description,reference_type,reference_id,branch_id',
            ])
            ->get()
            ->sortBy(function ($line) {
                // Sort: date ASC → journal_entry_id ASC → line id ASC
                return [
                    $line->journalEntry->date->format('Y-m-d'),
                    str_pad($line->journal_entry_id, 12, '0', STR_PAD_LEFT),
                    str_pad($line->id, 12, '0', STR_PAD_LEFT),
                ];
            });

        // Compute running balance in-memory
        $runningBalance = $openingBalance;
        $entries        = [];

        foreach ($lines as $line) {
            $debit  = (float) $line->debit;
            $credit = (float) $line->credit;

            $movement = $debitNormal
                ? ($debit - $credit)
                : ($credit - $debit);

            $runningBalance += $movement;

            $ref = '-';
            if ($line->journalEntry->reference_type) {
                $ref = class_basename($line->journalEntry->reference_type) . ' #' . $line->journalEntry->reference_id;
            }

            $entries[] = [
                'date'            => $line->journalEntry->date,
                'reference'       => $ref,
                'description'     => $line->journalEntry->description,
                'debit'           => $debit,
                'credit'          => $credit,
                'running_balance' => $runningBalance,
            ];
        }

        return [
            'account'         => $account,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'opening_balance' => $openingBalance,
            'entries'         => $entries,
            'closing_balance' => $runningBalance,
            'total_debit'     => collect($entries)->sum('debit'),
            'total_credit'    => collect($entries)->sum('credit'),
        ];
    }

    // =========================================================================
    // PROFIT & LOSS
    // =========================================================================

    /**
     * Profit & Loss Statement.
     *
     * Revenue = credit-normal balance on income accounts within period
     * Expenses = debit-normal balance on expense accounts within period
     * Net Profit = Revenue - Expenses
     */
    public function getProfitAndLoss(int $companyId, Carbon $startDate, Carbon $endDate, ?int $branchId = null): array
    {
        $income   = $this->getAccountBalancesForType($companyId, 'income',   $startDate, $endDate, $branchId);
        $expenses = $this->getAccountBalancesForType($companyId, 'expense',  $startDate, $endDate, $branchId);

        $totalIncome   = $income->sum('balance');
        $totalExpenses = $expenses->sum('balance');
        $netProfit     = $totalIncome - $totalExpenses;

        return [
            'income'         => $income,
            'total_income'   => $totalIncome,
            'expenses'       => $expenses,
            'total_expenses' => $totalExpenses,
            'net_profit'     => $netProfit,
            'start_date'     => $startDate,
            'end_date'       => $endDate,
        ];
    }

    // =========================================================================
    // BALANCE SHEET
    // =========================================================================

    /**
     * Balance Sheet (snapshot up to $date).
     *
     * Assets = Liabilities + Equity
     * Retained Earnings = cumulative net profit from inception to $date
     * (Never stored; computed dynamically from journal entries)
     */
    public function getBalanceSheet(int $companyId, Carbon $date, ?int $branchId = null): array
    {
        $inception = Carbon::create(1900, 1, 1);

        $assets      = $this->getAccountBalancesForType($companyId, 'asset',     $inception, $date, $branchId);
        $liabilities = $this->getAccountBalancesForType($companyId, 'liability', $inception, $date, $branchId);
        $equity      = $this->getAccountBalancesForType($companyId, 'equity',    $inception, $date, $branchId);

        // Retained earnings = cumulative net profit to date (computed, never stored)
        $pnl             = $this->getProfitAndLoss($companyId, $inception, $date, $branchId);
        $retainedEarnings = $pnl['net_profit'];

        $totalAssets      = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity      = $equity->sum('balance') + $retainedEarnings;

        $difference   = abs($totalAssets - ($totalLiabilities + $totalEquity));
        $isBalanced   = $difference < 0.01;

        return [
            'assets'             => $assets,
            'total_assets'       => $totalAssets,
            'liabilities'        => $liabilities,
            'total_liabilities'  => $totalLiabilities,
            'equity'             => $equity,
            'retained_earnings'  => $retainedEarnings,
            'total_equity'       => $totalEquity,
            'total_liab_equity'  => $totalLiabilities + $totalEquity,
            'is_balanced'        => $isBalanced,
            'difference'         => $difference,
            'as_of_date'         => $date,
        ];
    }

    // =========================================================================
    // CASH FLOW STATEMENT
    // =========================================================================

    /**
     * Cash Flow Statement (Indirect-inspired direct classification).
     *
     * Classification is driven by accounts.cash_flow_category column.
     * Categories: operating | investing | financing | none
     *
     * Each category shows:
     *   - List of accounts with their net movement in the period
     *   - Sub-total
     *
     * Final output:
     *   Opening Cash = sum of cash/bank account balances BEFORE period
     *   Net Movement = Operating + Investing + Financing
     *   Closing Cash  = Opening + Net Movement
     */
    public function getCashFlow(int $companyId, Carbon $startDate, Carbon $endDate, ?int $branchId = null): array
    {
        // Fetch all accounts for company with their cash_flow_category
        $accounts = Account::where('is_active', true)
            ->whereIn('cash_flow_category', ['operating', 'investing', 'financing'])
            ->get();

        $sections = [
            'operating'  => collect(),
            'investing'  => collect(),
            'financing'  => collect(),
        ];

        foreach ($accounts as $account) {
            $raw = $this->sumAccountLines($account->id, $startDate, $endDate, $branchId, true);

            // Net movement = using normal balance convention
            $debitNormal = $this->isDebitNormal($account->account_type);
            $net = $debitNormal
                ? ($raw['debit'] - $raw['credit'])
                : ($raw['credit'] - $raw['debit']);

            if ($net == 0) {
                continue;
            }

            $category = $account->cash_flow_category;

            $sections[$category]->push([
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'net'          => $net,
            ]);
        }

        $operatingTotal  = $sections['operating']->sum('net');
        $investingTotal  = $sections['investing']->sum('net');
        $financingTotal  = $sections['financing']->sum('net');
        $netMovement     = $operatingTotal + $investingTotal + $financingTotal;

        // Opening cash balance = Cash & Bank accounts (account_type=asset, cash_flow_category=operating)
        // from inception to day before period start
        $cashAccounts = Account::where('is_active', true)
            ->where('account_type', 'asset')
            ->where('cash_flow_category', 'operating')
            ->get();

        $openingCash = 0;
        foreach ($cashAccounts as $ca) {
            $raw = $this->sumAccountLines($ca->id, Carbon::create(1900, 1, 1), $startDate->copy()->subDay(), $branchId, true);
            $openingCash += ($raw['debit'] - $raw['credit']); // asset = debit normal
        }

        return [
            'operating'       => $sections['operating'],
            'operating_total' => $operatingTotal,
            'investing'       => $sections['investing'],
            'investing_total' => $investingTotal,
            'financing'       => $sections['financing'],
            'financing_total' => $financingTotal,
            'net_movement'    => $netMovement,
            'opening_cash'    => $openingCash,
            'closing_cash'    => $openingCash + $netMovement,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
        ];
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Get normalised balances for all accounts of a given type within a period.
     */
    protected function getAccountBalancesForType(
        int $companyId,
        string $type,
        Carbon $start,
        Carbon $end,
        ?int $branchId = null
    ): Collection {
        return Account::where('account_type', $type)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($start, $end, $branchId) {
                $raw     = $this->sumAccountLines($account->id, $start, $end, $branchId, true);
                $balance = $this->isDebitNormal($account->account_type)
                    ? ($raw['debit'] - $raw['credit'])
                    : ($raw['credit'] - $raw['debit']);

                return [
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'balance'      => $balance,
                ];
            })
            ->filter(fn($item) => $item['balance'] != 0)
            ->values();
    }

    /**
     * Core data fetcher.
     * Sums journal_entry_lines for an account within a date range.
     * Branch filter is applied to journal_entries header.
     * Only POSTED entries included.
     *
     * @param  bool  $raw  If true, returns ['debit'=>X,'credit'=>Y]. If false, returns normalised float.
     */
    protected function sumAccountLines(
        int $accountId,
        Carbon $start,
        Carbon $end,
        ?int $branchId,
        bool $raw = false
    ): mixed {
        $totals = DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->when($branchId, fn($q) => $q->where('journal_entries.branch_id', $branchId))
            ->selectRaw('SUM(journal_entry_lines.debit) as total_debit, SUM(journal_entry_lines.credit) as total_credit')
            ->first();

        $debit  = (float) ($totals->total_debit  ?? 0);
        $credit = (float) ($totals->total_credit ?? 0);

        if ($raw) {
            return ['debit' => $debit, 'credit' => $credit];
        }

        // Single account normalised balance (used for opening balance calc)
        // Will be interpreted by caller according to account type
        return $debit - $credit;
    }

    /**
     * Whether an account type has a debit normal balance.
     * Debit normal:  asset, expense
     * Credit normal: income, liability, equity
     */
    protected function isDebitNormal(string $accountType): bool
    {
        return in_array($accountType, ['asset', 'expense']);
    }

    /**
     * Normalize a raw DR−CR value based on account type.
     * Returns the natural balance sign consistent with the account type.
     */
    protected function normalizeBalance(string $accountType, float $rawDrMinusCr): float
    {
        // Debit-normal: positive means debit balance (healthy)
        if ($this->isDebitNormal($accountType)) {
            return $rawDrMinusCr;
        }
        // Credit-normal: positive means credit balance (healthy) — flip sign
        return -$rawDrMinusCr;
    }

    /**
     * Get Sales Tax Summary (VAT Collected vs VAT Paid).
     */
    public function getTaxSummary(int $companyId, Carbon $startDate, Carbon $endDate): array
    {
        $vatAccount = Account::where('account_code', '2013')
            ->first();

        if (!$vatAccount) {
            return [
                'vat_collected' => 0,
                'vat_paid' => 0,
                'net_payable' => 0,
                'period' => ['start' => $startDate, 'end' => $endDate],
            ];
        }

        $totals = DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $vatAccount->id)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('SUM(journal_entry_lines.credit) as collected, SUM(journal_entry_lines.debit) as paid')
            ->first();

        $collected = (float)($totals->collected ?? 0);
        $paid = (float)($totals->paid ?? 0);

        return [
            'vat_collected' => $collected,
            'vat_paid' => $paid,
            'net_payable' => $collected - $paid,
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ]
        ];
    }
}
