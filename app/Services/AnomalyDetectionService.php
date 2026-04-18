<?php

namespace App\Services;

use App\Models\Anomaly;
use App\Models\Company;
use App\Models\FinancialTransaction;
use App\Models\Vehicle;
use App\Models\BusProfitabilityMetric;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnomalyDetectionService
{
    /**
     * Detect anomalies for a company.
     */
    public function detectAnomalies(Company $company)
    {
        $this->detectFinancialSpikes($company);
        $this->detectInefficientAssets($company);
    }

    /**
     * Detect unexpected spikes in expenses (Z-Score > 3).
     */
    private function detectFinancialSpikes(Company $company)
    {
        // 1. Get recent expenses (last 7 days?) vs historical average (last 90 days?)
        // Simplifying: Look at individual large transactions in the last 24 hours 
        // compared to daily average of last 30 days.

        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);

        // Calculate Daily Expense Average (per transaction or daily total?)
        // Let's look for *single transactions* that are huge outliers.

        $stats = DB::table('financial_transactions')
            ->join('journal_entries', 'financial_transactions.id', '=', 'journal_entries.transaction_id')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->where('accounts.account_type', 'expense')
            ->whereBetween('financial_transactions.transaction_date', [$startDate, $endDate->copy()->subDay()]) // Exclude today/recent for baseline
            ->selectRaw('AVG(journal_entries.debit) as avg_amount, STDDEV(journal_entries.debit) as std_dev')
            ->first();

        $avgContext = $stats->avg_amount ?? 0;
        $stdDev = $stats->std_dev ?? 0;

        Log::info("Anomaly Debug: Avg=$avgContext, StdDev=$stdDev");

        if ($stdDev == 0) {
            $stdDev = 1; // Prevent division by zero, treat as very low variance
        }

        // Check recent transactions (last 7 days to catch up)
        $recentTransactions = FinancialTransaction::where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['journalEntries' => function ($q) {
                $q->whereHas('account', function ($sq) {
                    $sq->where('account_type', 'expense');
                });
            }])
            ->get();

        Log::info("Found " . $recentTransactions->count() . " recent transactions.");

        foreach ($recentTransactions as $tx) {
            foreach ($tx->journalEntries as $entry) {
                if ($entry->debit > 0) {
                    $zScore = ($entry->debit - $avgContext) / $stdDev;
                    Log::info("Tx {$tx->id}: Debit={$entry->debit}, Avg={$avgContext}, StdDev={$stdDev}, Z-Score={$zScore}");

                    if ($zScore > 3) {
                        // ANOMALY!
                        $this->logAnomaly(
                            $company,
                            'financial_spike',
                            'high',
                            "Unexpectedly high expense transaction: {$tx->description}",
                            $entry->debit,
                            $avgContext,
                            $tx
                        );
                    }
                }
            }
        }
    }

    /**
     * Detect vehicles with high maintenance but low utilization.
     */
    private function detectInefficientAssets(Company $company)
    {
        // Definition: Utilization < 10% AND Maintenance > $500 in last 30 days.

        $vehicles = Vehicle::where('status', 'active')->get();
        // Ideally we'd use FleetAnalyticsService for metrics, but let's query raw for speed in detection.

        $lastMonth = Carbon::now()->subDays(30);

        foreach ($vehicles as $vehicle) {
            // Utilization (Days Rented / 30)
            // Simplified: check total booking days.
            $daysRented = DB::connection('tenant')->table('bookings')
                ->where('vehicle_id', $vehicle->id)
                ->where('status', 'confirmed')
                ->where('pickup_time', '>=', $lastMonth)
                ->count(); // Rough proxy for days if 1 booking = 1 day, else sum duration.

            // Maintenance Cost
            // Assuming we track maintenance cost via transactions linked to vehicle? 
            // Or use BusProfitabilityMetric (last month).

            $metric = BusProfitabilityMetric::where('vehicle_id', $vehicle->id)
                ->orderBy('month_year', 'desc')
                ->first();

            if ($metric) {
                $utilizationRate = $metric->days_rented / 30; // 0.0 to 1.0
                $maintCost = $metric->maintenance_cost;

                if ($utilizationRate < 0.10 && $maintCost > 500) {
                    $this->logAnomaly(
                        $company,
                        'inefficient_asset',
                        'medium',
                        "Vehicle {$vehicle->vehicle_number} has high maintenance ($$maintCost) but low utilization (" . number_format($utilizationRate * 100, 1) . "%)",
                        $maintCost,
                        0, // Expected: Low cost for low use
                        $vehicle
                    );
                }
            }
        }
    }

    private function logAnomaly(Company $company, $type, $severity, $description, $detected, $expected, $relatedModel = null)
    {
        // Prevent duplicate alerts for same model today
        $exists = Anomaly::where('type', $type)
            ->where('related_model_type', $relatedModel ? get_class($relatedModel) : null)
            ->where('related_model_id', $relatedModel ? $relatedModel->id : null)
            ->where('created_at', '>=', Carbon::today())
            ->exists();

        if (!$exists) {
            Anomaly::create([
                'type' => $type,
                'severity' => $severity,
                'description' => $description,
                'detected_value' => $detected,
                'expected_value' => $expected,
                'related_model_type' => $relatedModel ? get_class($relatedModel) : null,
                'related_model_id' => $relatedModel ? $relatedModel->id : null,
                'status' => 'open'
            ]);

            // Trigger Notification Job here (Phase 6.7?)
            Log::warning("Anomaly Detected: $description");
        }
    }
}
