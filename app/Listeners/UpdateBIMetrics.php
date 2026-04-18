<?php

namespace App\Listeners;

use App\Events\RentalCompleted;
use App\Models\BusProfitabilityMetric;
use App\Models\DailyBranchMetric;
use App\Models\DriverPerformanceMetric;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateBIMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(RentalCompleted $event): void
    {
        $rental = $event->rental;

        // 1. Update Daily Branch Metrics
        $this->updateBranchMetrics($rental);

        // 2. Update Bus Profitability Metrics
        if ($rental->vehicle_id) {
            $this->updateBusMetrics($rental);
        }

        // 3. Update Driver Performance Metrics
        if ($rental->driver_id) {
            $this->updateDriverMetrics($rental);
        }
    }

    protected function updateBranchMetrics($rental)
    {
        $date = $rental->end_datetime->toDateString(); // Use end date for revenue realization

        DB::transaction(function () use ($rental, $date) {
            $metric = DailyBranchMetric::firstOrCreate(
                [
                    'company_id' => $rental->company_id,
                    'branch_id' => $rental->branch_id,
                    'date' => $date
                ],
                [
                    'total_revenue' => 0,
                    'rentals_count' => 0
                ]
            );

            $metric->increment('total_revenue', $rental->final_amount);
            $metric->increment('rentals_count');

            // Note: Expenses would be updated by a separate listener on ExpenseCreated event
        });
    }

    protected function updateBusMetrics($rental)
    {
        $monthYear = $rental->end_datetime->format('Y-m');

        DB::transaction(function () use ($rental, $monthYear) {
            $metric = BusProfitabilityMetric::firstOrCreate(
                [
                    'vehicle_id' => $rental->vehicle_id,
                    'month_year' => $monthYear
                ],
                [
                    'total_revenue' => 0,
                    'days_rented' => 0,
                    'total_km' => 0,
                    'net_profit' => 0
                ]
            );

            // Calculate rented days
            $days = $rental->actual_end_datetime->diffInDays($rental->actual_start_datetime) ?: 1;

            // Calculate KM
            $km = $rental->odometer_end - $rental->odometer_start;
            if ($km < 0) $km = 0; // Guard against bad data

            $metric->increment('total_revenue', $rental->final_amount);
            $metric->increment('days_rented', $days);
            $metric->increment('total_km', $km);
            $metric->increment('net_profit', $rental->final_amount); // Subtract costs in a separate step or improved logic
        });
    }

    protected function updateDriverMetrics($rental)
    {
        $monthYear = $rental->end_datetime->format('Y-m');

        DB::transaction(function () use ($rental, $monthYear) {
            $metric = DriverPerformanceMetric::firstOrCreate(
                [
                    'user_id' => $rental->driver_id, // Assuming driver_id links to users table
                    'month_year' => $monthYear
                ],
                [
                    'trips_completed' => 0,
                    'total_km_driven' => 0,
                    'total_hours_driven' => 0,
                    'safety_score' => 100.00
                ]
            );

            // Calculate Metrics
            $km = $rental->odometer_end - $rental->odometer_start;
            if ($km < 0) $km = 0;

            $hours = $rental->actual_end_datetime->diffInHours($rental->actual_start_datetime);

            $metric->increment('trips_completed');
            $metric->increment('total_km_driven', $km);
            $metric->increment('total_hours_driven', $hours);

            // Safety Score Logic (Simplified Mock)
            // In real world, we'd check 'telematics_events' table for this trip.
            // For now, we assume perfect score unless specific fines exist.
            // If we had $rental->fines(), we could deduct.
        });
    }
}
