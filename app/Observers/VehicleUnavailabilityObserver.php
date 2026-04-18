<?php

namespace App\Observers;

use App\Models\VehicleUnavailability;
use App\Models\Vehicle;

class VehicleUnavailabilityObserver
{
    /**
     * Handle the VehicleUnavailability "created" event.
     */
    public function created(VehicleUnavailability $unavailability): void
    {
        if ($unavailability->vehicle) {
            $unavailability->vehicle->update(['status' => Vehicle::STATUS_MAINTENANCE]);
        }
    }

    /**
     * Handle the VehicleUnavailability "deleted" event.
     */
    public function deleted(VehicleUnavailability $unavailability): void
    {
        if ($unavailability->vehicle) {
            $unavailability->vehicle->update(['status' => Vehicle::STATUS_AVAILABLE]);
        }
    }
}
