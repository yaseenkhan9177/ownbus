<?php

namespace App\Services;

use App\Models\Company;
use App\Models\FinancialForecast;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForecastingService
{
    /**
     * Generate financial forecasts for a company.
     *
     * @param Company $company
     * @param string $metricType 'revenue' or 'expense'
     * @param int $monthsToProject
     * @return void
     */
    public function generateForecast(Company $company, string $metricType, int $monthsToProject = 6)
    {
        // 1. Fetch Historical Data (Last 12 Months)
        $endDate = Carbon::now()->startOfMonth();
        $startDate = $endDate->copy()->subMonths(12);

        // Account Type Filtering
        // Assuming 'Revenue' accounts are credit-normal, 'Expense' are debit-normal.
        // We aggregate based on account type.

        $accountType = $metricType === 'revenue' ? 'income' : 'expense';

        $monthlyData = JournalEntry::join('financial_transactions', 'journal_entries.transaction_id', '=', 'financial_transactions.id')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->whereBetween('financial_transactions.transaction_date', [$startDate, $endDate])
            ->where('accounts.account_type', $accountType)
            ->selectRaw('
                YEAR(financial_transactions.transaction_date) as year, 
                MONTH(financial_transactions.transaction_date) as month, 
                SUM(journal_entries.credit - journal_entries.debit) as net_amount
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        // Prepare Data Points for Regression
        // X = Month Index (0, 1, 2...), Y = Amount
        $points = [];
        $index = 0;

        // Fill gaps if any? For simplicity, we stick to retrieved points.
        foreach ($monthlyData as $data) {
            $amount = $metricType === 'revenue' ? $data->net_amount : - ($data->net_amount); // Expenses usually debit positive, but if credit-debit, it's negative.
            // If metric is expense, we want positive magnitude.
            // Revenue (Credit - Debit) -> Positive
            // Expense (Credit - Debit) -> Negative. So negate it.

            $points[] = ['x' => $index++, 'y' => (float) $amount];
        }

        if (count($points) < 2) {
            Log::warning("Not enough data to forecast $metricType for Company {$company->id}");
            return;
        }

        // 2. Linear Regression (Least Squares)
        list($slope, $intercept) = $this->calculateLinearRegression($points);

        // 3. Project Future Months
        $lastMonth = Carbon::now()->startOfMonth();

        // Clear existing forecasts for this type to avoid duplicates? Or update?
        // Let's delete future forecasts generated previously for this run's scope.
        FinancialForecast::where('metric_type', $metricType)
            ->where('forecast_date', '>=', $lastMonth->format('Y-m-d'))
            ->delete();

        for ($i = 0; $i < $monthsToProject; $i++) {
            $futureX = $index + $i;
            $predictedY = ($slope * $futureX) + $intercept;

            // Confidence Score? 
            // Simple approach: based on R-squared or just hardcoded/placeholder for now.
            // Let's use a dummy confidence derived from data point count (max 0.9).
            $confidence = min(0.9, 0.5 + (count($points) * 0.02));

            FinancialForecast::create([
                'forecast_date' => $lastMonth->copy()->addMonths($i + 1)->format('Y-m-d'),
                'metric_type' => $metricType,
                'predicted_value' => max(0, $predictedY), // Floor at 0
                'confidence_score' => $confidence
            ]);
        }
    }

    private function calculateLinearRegression(array $points): array
    {
        $n = count($points);
        if ($n == 0) return [0, 0];

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumXX = 0;

        foreach ($points as $point) {
            $x = $point['x'];
            $y = $point['y'];
            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumXX += ($x * $x);
        }

        $denominator = ($n * $sumXX) - ($sumX * $sumX);
        if ($denominator == 0) return [0, 0];

        $slope = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
        $intercept = ($sumY - ($slope * $sumX)) / $n;

        return [$slope, $intercept];
    }
}
