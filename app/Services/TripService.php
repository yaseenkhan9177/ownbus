<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Rental;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TripService
{
    /**
     * Create a pending trip record when a rental is confirmed/assigned.
     */
    public function createFromRental(Rental $rental): Trip
    {
        return Trip::create([
            'uuid'             => (string) Str::uuid(),
            'rental_id'        => $rental->id,
            'driver_id'        => $rental->driver_id,
            'vehicle_id'       => $rental->vehicle_id,
            'branch_id'        => $rental->branch_id,
            'status'           => Trip::STATUS_PENDING,
            'scheduled_start'  => $rental->start_date,
            'scheduled_end'    => $rental->end_date,
            'pickup_location'  => $rental->pickup_location,
            'dropoff_location' => $rental->dropoff_location,
            'created_by'       => auth()->id(),
        ]);
    }

    /**
     * Start a trip — records actual start time and odometer.
     */
    public function start(Trip $trip, array $data = []): Trip
    {
        $trip->update([
            'status'         => Trip::STATUS_IN_PROGRESS,
            'actual_start'   => now(),
            'odometer_start' => $data['odometer_start'] ?? null,
            'start_lat'      => $data['lat'] ?? null,
            'start_lng'      => $data['lng'] ?? null,
        ]);

        return $trip->fresh();
    }

    /**
     * Complete a trip — records end time, odometer, distance, duration.
     */
    public function complete(Trip $trip, array $data = []): Trip
    {
        $actualEnd = now();
        $duration  = null;

        if ($trip->actual_start) {
            $duration = (int) $trip->actual_start->diffInMinutes($actualEnd);
        }

        $odometerEnd  = $data['odometer_end'] ?? null;
        $distanceKm   = ($odometerEnd && $trip->odometer_start)
            ? max(0, $odometerEnd - $trip->odometer_start)
            : null;

        $trip->update([
            'status'            => Trip::STATUS_COMPLETED,
            'actual_end'        => $actualEnd,
            'duration_minutes'  => $duration,
            'odometer_end'      => $odometerEnd,
            'distance_km'       => $distanceKm,
            'end_lat'           => $data['lat'] ?? null,
            'end_lng'           => $data['lng'] ?? null,
            'driver_notes'      => $data['notes'] ?? null,
            'driver_rating'     => $data['rating'] ?? null,
            'fuel_used_liters'  => $data['fuel_used_liters'] ?? null,
        ]);

        // Update the vehicle's odometer if end odometer provided
        if ($odometerEnd && $trip->vehicle) {
            $trip->vehicle->update(['current_odometer' => $odometerEnd]);
        }

        return $trip->fresh();
    }

    /**
     * Cancel a trip.
     */
    public function cancel(Trip $trip, string $reason = ''): Trip
    {
        $trip->update([
            'status'       => Trip::STATUS_CANCELLED,
            'driver_notes' => $reason ?: $trip->driver_notes,
        ]);

        return $trip->fresh();
    }

    /**
     * Get or create a pending trip for a rental.
     * Ensures exactly one pending trip exists per rental.
     */
    public function ensureTripForRental(Rental $rental): Trip
    {
        $existing = Trip::where('rental_id', $rental->id)
            ->whereIn('status', [Trip::STATUS_PENDING, Trip::STATUS_IN_PROGRESS])
            ->first();

        if ($existing) {
            return $existing;
        }

        return $this->createFromRental($rental);
    }

    /**
     * Fleet-level trip statistics.
     */
    public function getFleetStats(): array
    {
        return [
            'total_trips'      => Trip::count(),
            'active_trips'     => Trip::active()->count(),
            'completed_today'  => Trip::completed()
                ->whereDate('actual_end', today())
                ->count(),
            'total_km_today'   => (int) Trip::completed()
                ->whereDate('actual_end', today())
                ->sum('distance_km'),
            'avg_duration_min' => (int) Trip::completed()
                ->avg('duration_minutes'),
        ];
    }
}
