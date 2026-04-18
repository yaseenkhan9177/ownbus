<?php

namespace App\Services;

use App\Models\BusProfitabilityMetric;
use App\Models\DriverPerformanceMetric;
use App\Models\Vehicle;
use App\Models\VehicleUnavailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FleetAnalyticsService
{
    /**
     * Get Fleet Utilization Rate for a specific period.
     * 
     * Formula: (Total Days Rented / (Total Vehicles * Days in Period)) * 100
     */
    public function getUtilizationRate(Carbon $start, Carbon $end, ?int $branchId = null): float
    {
        $cacheKey = "utilization_{$start->format('Ymd')}_{$end->format('Ymd')}_{$branchId}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($start, $end, $branchId) {
            $daysInPeriod = $start->diffInDays($end) + 1;

            $vehiclesQuery = Vehicle::query()->where('status', '!=', 'sold');
            if ($branchId) {
                $vehiclesQuery->where('branch_id', $branchId);
            }
            $totalVehicles = $vehiclesQuery->count();

            if ($totalVehicles == 0) return 0.0;

            $startMonth = $start->format('Y-m');
            $endMonth = $end->format('Y-m');

            $metricsQuery = BusProfitabilityMetric::whereBetween('month_year', [$startMonth, $endMonth]);

            if ($branchId) {
                $metricsQuery->whereHas('vehicle', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            }

            $totalDaysRented = $metricsQuery->sum('days_rented');

            $capacityDays = $totalVehicles * $daysInPeriod;
            if ($capacityDays == 0) return 0.0;

            return round(($totalDaysRented / $capacityDays) * 100, 2);
        });
    }

    /**
     * Get Revenue per KM.
     */
    public function getRevenuePerKm(Carbon $start, Carbon $end, ?int $branchId = null): float
    {
        $startMonth = $start->format('Y-m');
        $endMonth = $end->format('Y-m');

        $query = BusProfitabilityMetric::whereBetween('month_year', [$startMonth, $endMonth]);

        if ($branchId) {
            $query->whereHas('vehicle', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $totalRevenue = $query->sum('total_revenue');
        $totalKm = $query->sum('total_km');

        if ($totalKm == 0) return 0.0;

        return round($totalRevenue / $totalKm, 2);
    }

    /**
     * Get Maintenance Cost per KM.
     */
    public function getMaintenanceCostPerKm(Carbon $start, Carbon $end, ?int $branchId = null): float
    {
        $startMonth = $start->format('Y-m');
        $endMonth = $end->format('Y-m');

        $query = BusProfitabilityMetric::whereBetween('month_year', [$startMonth, $endMonth]);

        if ($branchId) {
            $query->whereHas('vehicle', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $totalMaintenance = $query->sum('maintenance_cost');
        $totalKm = $query->sum('total_km');

        if ($totalKm == 0) return 0.0;

        return round($totalMaintenance / $totalKm, 2);
    }

    /**
     * Detect Idle Vehicles (No bookings in last X days).
     */
    public function getIdleVehicles(int $daysThreshold = 7, ?int $branchId = null)
    {
        $thresholdDate = Carbon::now()->subDays($daysThreshold);

        $query = Vehicle::whereDoesntHave('bookings', function ($q) use ($thresholdDate) {
            $q->where('start_date', '>=', $thresholdDate);
        })->where('status', 'active');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    /**
     * Get Top Performing Drivers based on Safety Score & Trips.
     */
    public function getTopDrivers(int $limit = 5, string $monthYear)
    {
        return DriverPerformanceMetric::with('user')
            ->where('month_year', $monthYear)
            ->orderByDesc('safety_score')
            ->orderByDesc('trips_completed')
            ->limit($limit)
            ->get();
    }
}
