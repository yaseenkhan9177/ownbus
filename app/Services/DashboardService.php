<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Rental;
use App\Models\Vehicle;
use App\Models\BusProfitabilityMetric;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\VehicleMaintenancePrediction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Get aggregate statistics for a company.
     * Now uses caching for improved performance.
     */
    public function getCompanyStats(Company $company): array
    {
        // Try cache first for fast response
        return $this->cacheService->cacheDashboardKPIs($company->id);
    }

    /**
     * Get performance ranking for buses.
     */
    public function getFleetPerformance(Company $company): array
    {
        $perf = BusProfitabilityMetric::with('vehicle')
            ->orderByDesc('net_profit')
            ->get();

        return [
            'top_performing' => $perf->take(5),
            'worst_performing' => $perf->reverse()->take(5),
        ];
    }

    /**
     * Get revenue comparison by branch.
     */
    public function getBranchComparison(Company $company): array
    {
        return Rental::join('branches', 'rentals.branch_id', '=', 'branches.id')
            ->select('branches.name', DB::raw('SUM(final_amount) as revenue'))
            ->groupBy('branches.name')
            ->get()
            ->toArray();
    }

    /**
     * Get predictive maintenance insights for the dashboard.
     */
    public function getPredictiveMaintenanceInfo(Company $company): array
    {
        $predictions = VehicleMaintenancePrediction::with('vehicle')
            ->orderBy('predicted_service_date', 'asc')
            ->get();

        $budgetNext30Days = $predictions->filter(function ($p) {
            return $p->predicted_service_date->lte(now()->addDays(30));
        })->sum(function ($p) {
            // Simple heuristic for predicted cost: avg of last few sets or standard 1500
            return 1500;
        });

        return [
            'predictions' => $predictions->take(10),
            'high_risk_count' => $predictions->where('risk_level', 'high')->count(),
            'avg_days_until_service' => $predictions->avg(function ($p) {
                return now()->diffInDays($p->predicted_service_date, false);
            }),
            'predicted_budget_30_days' => $budgetNext30Days,
        ];
    }
}
