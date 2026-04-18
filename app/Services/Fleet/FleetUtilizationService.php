<?php

namespace App\Services\Fleet;

use App\Models\Vehicle;
use App\Models\BusUtilizationLog;
use App\Models\Company;
use App\Models\Rental;
use Carbon\Carbon;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;

class FleetUtilizationService
{
    /**
     * Get utilization rate for the company.
     * Formula: (Total booked days) / (Total available vehicle-days)
     */
    public function getUtilizationRate(Company $company, int $days = 30): float
    {
        return CacheService::rememberTagged(["utilization"], "utilization:rate:{$days}", CacheService::TTL_MEDIUM, function () use ($company, $days) {
            $totalVehicles = Vehicle::where('status', '!=', 'inactive')->count();

            if ($totalVehicles === 0) {
                return 0.0;
            }

            $startDate = Carbon::now()->subDays($days);
            $endDate = Carbon::now();

            // Calculate distinct days occupied
            // If BusUtilizationLog is robust:
            $rentedDays = BusUtilizationLog::where('date', '>=', $startDate)
                ->count();

            // Fallback to calculation from Rentals if logs are potentially empty or unreliable
            if ($rentedDays === 0) {
                // Sum of duration of all rentals in period (capped by period boundaries)
                // This is a simplified estimation for fallback
                $rentedSeconds = Rental::where('status', '!=', 'cancelled')
                    ->where('end_date', '>=', $startDate)
                    ->where('start_date', '<=', $endDate)
                    ->get()
                    ->reduce(function ($carry, $rental) use ($startDate, $endDate) {
                        $start = $rental->start_date < $startDate ? $startDate : $rental->start_date;
                        $end = $rental->end_date > $endDate ? $endDate : $rental->end_date;

                        if ($end > $start) {
                            $carry += $end->diffInSeconds($start);
                        }
                        return $carry;
                    }, 0);

                $rentedDays = $rentedSeconds / 86400; // Convert seconds to days
            }

            // Potential Capacity: Vehicles * Days
            $potentialCapacity = $totalVehicles * $days;

            if ($potentialCapacity === 0) {
                return 0.0;
            }

            return round(($rentedDays / $potentialCapacity) * 100, 2);
        });
    }

    /**
     * Identify idle vehicles (not rented in last X days).
     */
    public function getIdleVehiclesCount(Company $company, int $days = 7): int
    {
        return CacheService::rememberTagged(["utilization"], "utilization:idle_count", CacheService::TTL_SHORT, function () use ($company, $days) {
            $cutoffDate = Carbon::now()->subDays($days);

            // Get IDs of vehicles that had activity via logs
            $activeIds = BusUtilizationLog::where('date', '>=', $cutoffDate)
                ->pluck('bus_id')
                ->unique()
                ->toArray();

            // Also check rentals directly to be safe (if logs aren't real-time)
            $activeRentalIds = Rental::where('status', '!=', 'cancelled')
                ->where('end_date', '>=', $cutoffDate)
                ->pluck('vehicle_id')
                ->unique()
                ->toArray();

            $allActiveIds = array_unique(array_merge($activeIds, $activeRentalIds));

            return Vehicle::whereNotIn('id', $allActiveIds)
                ->where('status', 'available')
                ->count();
        });
    }

    /**
     * Get utilization trend for the last 90 days.
     * Return structured chart data.
     */
    public function getUtilizationTrend(Company $company): array
    {
        return CacheService::rememberTagged(["utilization"], "utilization:trend", CacheService::TTL_LONG, function () use ($company) {
            $days = 30; // Show last 30 days for clarity
            $startDate = Carbon::now()->subDays($days);

            // DB Aggregation
            $data = DB::connection('tenant')->table('bus_utilization_logs')
                ->where('date', '>=', $startDate)
                ->select(
                    'date',
                    DB::raw('COUNT(DISTINCT bus_id) as active_count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Format for Chart.js
            $labels = [];
            $values = [];

            // Fill in missing dates with 0
            $period = \Carbon\CarbonPeriod::create($startDate, Carbon::now());

            // Create a map for easy lookup
            $dataMap = $data->pluck('active_count', 'date')->all();

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->format('M d'); // readable label
                // Check exact date match or date string match depending on DB driver return type
                // Converting logic to ensure match works
                $matched = false;
                foreach ($dataMap as $key => $val) {
                    if (str_starts_with($key, $dateStr)) {
                        $values[] = $val;
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    $values[] = 0;
                }
            }

            return [
                'labels' => $labels,
                'values' => $values
            ];
        });
    }
}
