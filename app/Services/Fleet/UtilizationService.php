<?php

namespace App\Services\Fleet;

use App\Models\Vehicle;
use App\Models\BusUtilizationLog;
use App\Models\Company;
use Carbon\Carbon;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UtilizationService
{
    /**
     * Get platform-wide or company-specific utilization heatmap.
     */
    public function getUtilizationRate(Company $company = null, int $days = 30): float
    {
        $cacheKey = $company ? "util_rate_comp_{$company->id}" : "util_rate_global";
        $tags = $company ? ["company:{$company->id}:analytics"] : ['saas:global'];

        return CacheService::rememberTagged($tags, $cacheKey, CacheService::TTL_MEDIUM, function () use ($company, $days) {
            $query = Vehicle::query();

            $totalVehicles = $query->count();
            if ($totalVehicles === 0) return 0.0;

            $rentedDays = BusUtilizationLog::query()
                ->where('date', '>=', Carbon::now()->subDays($days))
                ->count(); // Assuming one log entry per vehicle per day rented

            return round(($rentedDays / ($totalVehicles * $days)) * 100, 2);
        });
    }

    /**
     * Identify idle vehicles (not rented in last X days).
     */
    public function getIdleVehicles(Company $company, int $days = 7): int
    {
        $activeIds = BusUtilizationLog::where('date', '>=', Carbon::now()->subDays($days))
            ->pluck('vehicle_id')
            ->unique();

        return Vehicle::whereNotIn('id', $activeIds)
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get utilization by branch for comparison.
     */
    public function getBranchUtilization(Company $company): array
    {
        return CacheService::rememberTagged(["company:{$company->id}:analytics"], "branch_util_{$company->id}", CacheService::TTL_MEDIUM, function () use ($company) {
            return DB::connection('tenant')->table('daily_branch_metrics')
                ->where('date', '>=', Carbon::now()->subDays(30))
                ->select('branch_id', DB::raw('AVG(active_vehicles_count) as avg_util'))
                ->groupBy('branch_id')
                ->get()
                ->toArray();
        });
    }
}
