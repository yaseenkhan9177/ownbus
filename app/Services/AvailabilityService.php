<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\DriverProfile;
use App\Models\VehicleUnavailability;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Check if a bus is available for a given time range.
     *
     * @param int $busId
     * @param Carbon $start
     * @param Carbon $end
     * @param int|null $excludeRentalId ID of rental to exclude (for updates)
     * @return bool
     */
    public function isBusAvailable(int $busId, Carbon $start, Carbon $end, ?int $excludeRentalId = null): bool
    {
        // 1. Check if bus exists and is active
        $bus = Vehicle::find($busId);
        if (!$bus || $bus->status !== 'active') {
            // 'active' meant available for rent. 'maintenance' is unavailable.
            // If status is 'reserved', we need to check if reserved for THIS rental or another.
            // For simplicity, let's assume 'active' is the baseline requirement.
            // However, a bus might be 'active' generally but booked for a specific time.
            if ($bus && in_array($bus->status, ['maintenance', 'retired'])) {
                return false;
            }
        }

        // 2. Check for overlapping rentals
        if ($this->checkOverlap($busId, 'vehicle_id', $start, $end, $excludeRentalId)) {
            return false;
        }

        // 3. Check for Maintenance/Unavailability
        if ($this->checkMaintenanceOverlap($busId, $start, $end)) {
            return false;
        }

        // 4. Check for Scheduled Predictive Maintenance
        return !\App\Models\MaintenancePrediction::where('vehicle_id', $busId)
            ->where('status', 'scheduled')
            ->whereDate('predicted_date', '>=', $start)
            ->whereDate('predicted_date', '<=', $end)
            ->exists();
    }

    /**
     * Check if a driver is available.
     *
     * @param int $driverId
     * @param Carbon $start
     * @param Carbon $end
     * @param int|null $excludeRentalId
     * @return bool
     */
    public function isDriverAvailable(int $driverId, Carbon $start, Carbon $end, ?int $excludeRentalId = null): bool
    {
        // 1. Check Driver Status (Leave, Suspended)
        $profile = DriverProfile::where('user_id', $driverId)->first();
        if ($profile && in_array($profile->status, ['leave', 'suspended'])) {
            return false;
        }

        // 2. Check Overlap
        return !$this->checkOverlap($driverId, 'driver_id', $start, $end, $excludeRentalId);
    }

    /**
     * Core overlap logic.
     */
    protected function checkOverlap(int $resourceId, string $column, Carbon $start, Carbon $end, ?int $excludeRentalId = null): bool
    {
        $query = Rental::where($column, $resourceId)
            ->whereIn('status', ['confirmed', 'assigned', 'dispatched']) // Only active bookings block availability
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            });

        if ($excludeRentalId) {
            $query->where('id', '!=', $excludeRentalId);
        }

        return $query->exists();
    }

    /**
     * Check overlap with VehicleUnavailability table.
     */
    protected function checkMaintenanceOverlap(int $vehicleId, Carbon $start, Carbon $end): bool
    {
        return VehicleUnavailability::where('vehicle_id', $vehicleId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_datetime', [$start, $end])
                    ->orWhereBetween('end_datetime', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_datetime', '<=', $start)
                            ->where('end_datetime', '>=', $end);
                    });
            })
            ->exists();
    }
}
