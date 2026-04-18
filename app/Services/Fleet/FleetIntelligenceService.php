<?php

namespace App\Services\Fleet;

use App\Models\BusProfitabilityMetric;
use App\Models\BusUtilizationLog;
use App\Models\Company;
use App\Models\Rental;
use App\Models\Vehicle;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FleetIntelligenceService
{
    protected UtilizationService $utilizationService;
    protected MaintenancePredictor $maintenancePredictor;

    public function __construct(
        UtilizationService $utilizationService,
        MaintenancePredictor $maintenancePredictor
    ) {
        $this->utilizationService = $utilizationService;
        $this->maintenancePredictor = $maintenancePredictor;
    }

    /**
     * Get aggregated KPI tiles for Company Owner.
     */
    public function getFleetROI(Company $company): float
    {
        return CacheService::rememberTagged(["company:{$company->id}:analytics"], "fleet_roi_{$company->id}", CacheService::TTL_LONG, function () use ($company) {
            $revenue = Rental::sum('final_amount');
            $costs = BusProfitabilityMetric::whereHas('vehicle')
                ->sum(DB::raw('fuel_cost + maintenance_cost'));

            if ($costs <= 0) return 0;
            return (($revenue - $costs) / $costs) * 100;
        });
    }

    public function getIdleCount(Company $company): int
    {
        return CacheService::rememberTagged(["company:{$company->id}:analytics"], "idle_count_{$company->id}", CacheService::TTL_SHORT, function () use ($company) {
            $activeVehicleIds = Rental::where('created_at', '>', now()->subDays(3))
                ->pluck('vehicle_id')
                ->unique();

            return Vehicle::whereNotIn('id', $activeVehicleIds)
                ->count();
        });
    }

    public function getDashboardKPIs(Company $company): array
    {
        return CacheService::rememberTagged(["company:{$company->id}:analytics"], "fleet_kpi_tiles_{$company->id}", CacheService::TTL_SHORT, function () use ($company) {
            return [
                'active_rentals' => Rental::count(), // Modified
                'revenue_today' => Rental::whereDate('created_at', today())
                    ->sum('final_amount'),
                'utilization_rate' => $this->utilizationService->getUtilizationRate($company),
                'maintenance_alerts' => count($this->maintenancePredictor->getUrgentMaintenance($company)),
                'fleet_roi' => $this->getFleetROI($company), // Added
                'idle_count' => $this->getIdleCount($company), // Added
                'pending_invoices' => DB::table('subscription_invoices')
                    ->where('status', 'pending')
                    ->count(),
            ];
        });
    }

    /**
     * Get financial performance overview for the company.
     */
    public function getProfitabilityStats(Company $company): array
    {
        return CacheService::rememberTagged(["company:{$company->id}:analytics"], "profit_stats_{$company->id}", CacheService::TTL_LONG, function () use ($company) {
            // Aggregating from bus_profitability_metrics
            $stats = DB::connection('tenant')->table('bus_profitability_metrics')
                ->join('vehicles', 'bus_profitability_metrics.vehicle_id', '=', 'vehicles.id')
                ->select(
                    DB::raw('SUM(total_revenue) as revenue'),
                    DB::raw('SUM(fuel_cost + maintenance_cost) as expenses'),
                    DB::raw('SUM(net_profit) as profit')
                )
                ->first();

            return [
                'revenue' => (float)($stats->revenue ?? 0),
                'expenses' => (float)($stats->expenses ?? 0),
                'profit' => (float)($stats->profit ?? 0),
                'margin' => $stats->revenue > 0 ? round(($stats->profit / $stats->revenue) * 100, 2) : 0,
            ];
        });
    }

    /**
     * Get monthly fuel consumption/expense trends for the company.
     */
    public function getFuelConsumptionTrends(Company $company): array
    {
        return CacheService::rememberTagged(["company:{$company->id}:analytics"], "fuel_trends_{$company->id}", CacheService::TTL_LONG, function () use ($company) {
            return DB::connection('tenant')->table('bus_profitability_metrics')
                ->join('vehicles', 'bus_profitability_metrics.vehicle_id', '=', 'vehicles.id')
                ->select(
                    DB::raw('DATE_FORMAT(bus_profitability_metrics.created_at, "%b") as month'),
                    DB::raw('SUM(fuel_cost) as total_fuel')
                )
                ->where('bus_profitability_metrics.created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('bus_profitability_metrics.created_at', 'asc')
                ->get()
                ->toArray();
        });
    }
}
