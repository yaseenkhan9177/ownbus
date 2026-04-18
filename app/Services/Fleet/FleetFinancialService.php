<?php

namespace App\Services\Fleet;

use App\Models\Company;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;

class FleetFinancialService
{
    /**
     * Get Profit and Loss snapshot.
     */
    public function getFinancialSnapshot(Company $company): array
    {
        return CacheService::rememberTagged(["company:{$company->id}:financials"], "fleet_financial_snapshot_{$company->id}", CacheService::TTL_LONG, function () use ($company) {
            // Aggregating from bus_profitability_metrics
            // Assuming this table holds pre-calculated costs and revenues per vehicle/trip

            $stats = DB::connection('tenant')->table('bus_profitability_metrics')
                ->join('vehicles', 'bus_profitability_metrics.vehicle_id', '=', 'vehicles.id')
                ->select(
                    DB::raw('SUM(total_revenue) as revenue'),
                    DB::raw('SUM(fuel_cost + maintenance_cost) as expenses'),
                    DB::raw('SUM(net_profit) as profit')
                )
                ->first();

            $revenue = (float)($stats->revenue ?? 0);
            $expenses = (float)($stats->expenses ?? 0);
            $profit = (float)($stats->profit ?? 0);

            return [
                'total_revenue' => $revenue,
                'total_expenses' => $expenses,
                'total_profit' => $profit,
                'margin_percent' => $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0,
                // 'outstanding_payments' => ... // Calculate from Invoices or Rentals
            ];
        });
    }

    /**
     * Get pending invoices count/amount (Receivables).
     */
    public function getReceivables(Company $company): array
    {
        return CacheService::rememberTagged(["company:{$company->id}:financials"], "fleet_receivables_{$company->id}", CacheService::TTL_MEDIUM, function () use ($company) {
            // Example: Pending Rental Invoices
            $pendingAmount = DB::connection('tenant')->table('rentals')
                ->where('payment_status', 'pending')
                ->sum('final_amount'); // Or balance_due if partial payments exist

            return [
                'pending_amount' => (float)$pendingAmount,
                'count' => DB::connection('tenant')->table('rentals')->where('payment_status', 'pending')->count(),
            ];
        });
    }
}
