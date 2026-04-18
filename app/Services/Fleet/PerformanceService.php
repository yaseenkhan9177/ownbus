<?php

namespace App\Services\Fleet;

use App\Models\Driver;
use App\Models\Rental;
use Carbon\Carbon;

class PerformanceService
{
    /**
     * Calculate performance metrics for a driver.
     */
    public function getDriverMetrics(Driver $driver): array
    {
        // Ensure we strictly look at stats where they were the driver
        $rentals = Rental::where('driver_id', $driver->id)
            ->whereIn('status', [Rental::STATUS_COMPLETED, 'closed'])
            ->get(); // Fetching all specific to driver for calculation (optimize with DB Aggregation for large scale)

        $totalTrips = $rentals->count();
        if ($totalTrips === 0) {
            return [
                'total_trips' => 0,
                'on_time_rate' => 100, // Default to 100 for optimism or 0
                'completion_rate' => 0,
                'avg_trip_duration_hrs' => 0,
            ];
        }

        $onTimeCount = $rentals->filter(function ($rental) {
            // If actual end exists and is <= scheduled end + 15 mins buffer
            if ($rental->actual_end_datetime && $rental->end_date) {
                return $rental->actual_end_datetime->lte($rental->end_date->addMinutes(15));
            }
            return true; // Assume on time if not recorded? Or treat as neutral. Let's assume on time.
        })->count();

        $totalHours = $rentals->sum(function ($rental) {
            if ($rental->actual_start_datetime && $rental->actual_end_datetime) {
                return $rental->actual_start_datetime->diffInHours($rental->actual_end_datetime);
            }
            return 0;
        });

        return [
            'total_trips' => $totalTrips,
            'on_time_rate' => round(($onTimeCount / $totalTrips) * 100, 1),
            'completion_rate' => 100, // For now assuming all fetched are completed/closed
            'avg_trip_duration_hrs' => round($totalHours / $totalTrips, 1),
        ];
    }

    /**
     * Get aggregated system-wide driver performance.
     */
    public function getFleetPerformanceStats(\App\Models\Company $company): array
    {
        // Placeholder for dashboard aggregation
        return [];
    }
}
