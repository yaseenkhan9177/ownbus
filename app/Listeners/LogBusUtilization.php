<?php

namespace App\Listeners;

use App\Events\RentalCompleted;
use App\Models\BusUtilizationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogBusUtilization implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RentalCompleted $event): void
    {
        $rental = $event->rental;

        if (!$rental->vehicle_id) {
            return;
        }

        // Calculate Usage
        $hours = 0;
        if ($rental->actual_start_datetime && $rental->actual_end_datetime) {
            $hours = $rental->actual_start_datetime->diffInHours($rental->actual_end_datetime);
        }

        $km = 0;
        if ($rental->odometer_end && $rental->odometer_start) {
            $km = max(0, $rental->odometer_end - $rental->odometer_start);
        }

        // Log utilization
        BusUtilizationLog::create([
            'vehicle_id' => $rental->vehicle_id,
            'rental_id' => $rental->id,
            'hours_used' => $hours,
            'km_used' => $km,
            'fuel_consumed' => 0, // Placeholder, maybe derived from efficiency or manual input later
            'date' => now()->toDateString(),
        ]);

        // Update Vehicle Odometer
        $rental->bus->update([
            'current_odometer' => $rental->odometer_end ?? $rental->bus->current_odometer
        ]);

        // Update Driver Stats? (Phase 2 extension)
        // If we had a driver_stats table or update profile:
        // $rental->driver->driverProfile->increment('total_trips');
    }
}
