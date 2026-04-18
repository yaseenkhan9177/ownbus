<?php

namespace App\Services;

use App\Models\Company;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * UAE VAT Service
 *
 * Calculates VAT summaries based on accounting journal lines.
 * UAE Standard VAT rate: 5%
 *
 * Output VAT = VAT charged to customers (on revenue accounts)
 * Input VAT  = VAT paid to suppliers (on expense accounts)
 * Net VAT Payable = Output VAT - Input VAT
 */
class VatService
{
    const UAE_VAT_RATE = 5.0;

    /**
     * Get full VAT summary for the current quarter.
     */
    public function getVatSummary(Company $company): array
    {
        $quarterStart = Carbon::now()->startOfQuarter();
        $quarterEnd   = Carbon::now()->endOfQuarter();

        $outputVat = $this->calculateOutputVat($company, $quarterStart, $quarterEnd);
        $inputVat  = $this->calculateInputVat($company, $quarterStart, $quarterEnd);
        $netVat    = $outputVat - $inputVat;

        return [
            'output_vat'        => round($outputVat, 2),
            'input_vat'         => round($inputVat, 2),
            'net_vat_payable'   => round($netVat, 2),
            'vat_this_quarter'  => $quarterStart->format('Q') . 'Q ' . $quarterStart->year,
            'quarter_label'     => 'Q' . $quarterStart->quarter . ' ' . $quarterStart->year,
            'quarter_start'     => $quarterStart->toDateString(),
            'quarter_end'       => $quarterEnd->toDateString(),
            'vat_rate'          => self::UAE_VAT_RATE,
        ];
    }

    /**
     * Output VAT = 5% of total revenue (credit on income/revenue accounts) this quarter.
     */
    protected function calculateOutputVat(Company $company, Carbon $from, Carbon $to): float
    {
        // Revenue accounts are 'income' type
        $revenueAccountIds = Account::where('account_type', 'income')
            ->where('vat_applicable', true)
            ->pluck('id');

        if ($revenueAccountIds->isEmpty()) {
            // Fallback: estimate from all income accounts
            $revenueAccountIds = Account::where('account_type', 'income')
                ->pluck('id');
        }

        $totalRevenue = JournalEntryLine::whereIn('account_id', $revenueAccountIds)
            ->whereHas('journalEntry', function ($q) use ($from, $to) {
                $q->whereBetween('date', [$from, $to]);
            })
            ->sum('credit');

        return $totalRevenue * (self::UAE_VAT_RATE / 100);
    }

    /**
     * Input VAT = 5% of total expenses (debit on expense accounts) this quarter.
     */
    protected function calculateInputVat(Company $company, Carbon $from, Carbon $to): float
    {
        $expenseAccountIds = Account::where('account_type', 'expense')
            ->where('vat_applicable', true)
            ->pluck('id');

        if ($expenseAccountIds->isEmpty()) {
            $expenseAccountIds = Account::where('account_type', 'expense')
                ->pluck('id');
        }

        $totalExpenses = JournalEntryLine::whereIn('account_id', $expenseAccountIds)
            ->whereHas('journalEntry', function ($q) use ($from, $to) {
                $q->whereBetween('date', [$from, $to]);
            })
            ->sum('debit');

        return $totalExpenses * (self::UAE_VAT_RATE / 100);
    }
}
