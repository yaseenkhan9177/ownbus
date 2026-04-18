<?php

namespace App\Services\Fleet;

use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;

class DispatchService
{
    /**
     * Suggest available drivers for a specific rental.
     * Logic:
     * 1. Must be a 'driver' role.
     * 2. Must belong to the same company.
     * 3. Must NOT have an overlapping rental.
     */
    public function suggestDrivers(Rental $rental)
    {
        $start = $rental->start_date;
        $end = $rental->end_date;

        // Get IDs of drivers who have overlapping rentals
        $busyDriverIds = Rental::query()
            ->where('id', '!=', $rental->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->whereIn('status', ['confirmed', 'assigned', 'dispatched', 'active'])
            ->pluck('driver_id')
            ->filter()
            ->unique()
            ->toArray();

        // Fetch available drivers
        return User::where('company_id', $rental->company_id)
            ->where('role', 'driver')
            ->whereNotIn('id', $busyDriverIds)
            ->get();
    }

    /**
     * Check if a specific driver is available for a time slot.
     */
    public function isDriverAvailable($driverId, $start, $end, $excludeRentalId = null)
    {
        $query = Rental::where('driver_id', $driverId)
            ->whereIn('status', ['confirmed', 'assigned', 'dispatched', 'active'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            });

        if ($excludeRentalId) {
            $query->where('id', '!=', $excludeRentalId);
        }

        return !$query->exists();
    }
}
