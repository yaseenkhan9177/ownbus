<?php

namespace App\Services\Fleet;

use App\Models\Rental;
use App\Models\Company;
use App\Models\Vehicle;
use App\Services\CacheService;
use Carbon\Carbon;

class FleetOperationsService
{
    /**
     * Get rental operation statistics.
     */
    public function getRentalStats(Company $company): array
    {
        return CacheService::rememberTagged(["company:{$company->id}:rentals"], "company:{$company->id}:rentals:stats", CacheService::TTL_SHORT, function () use ($company) {
            $now = Carbon::now();

            return [
                'upcoming' => Rental::where('status', Rental::STATUS_CONFIRMED)
                    ->where('start_date', '>', $now)
                    ->where('start_date', '<=', $now->copy()->addHours(48))
                    ->count(),
                'active' => Rental::where('status', Rental::STATUS_ACTIVE)
                    ->count(),
                'pending_assignment' => Rental::where('status', Rental::STATUS_CONFIRMED)
                    ->whereNull('vehicle_id')
                    ->count(),
                'conflicts' => $this->getConflictCount($company),
            ];
        });
    }

    /**
     * Detect number of potential conflicts.
     * A conflict is when a vehicle is double booked or assigned to a rental while in maintenance.
     */
    public function getConflictCount(Company $company): int
    {
        return CacheService::rememberTagged(["company:{$company->id}:rentals"], "company:{$company->id}:rentals:conflicts", CacheService::TTL_SHORT, function () use ($company) {
            return Rental::query()
                ->join('rentals as r2', function ($join) {
                    $join->on('rentals.vehicle_id', '=', 'r2.vehicle_id')
                        ->whereColumn('rentals.id', '<', 'r2.id');
                })
                ->whereIn('rentals.status', [Rental::STATUS_CONFIRMED, Rental::STATUS_ACTIVE])
                ->whereIn('r2.status', [Rental::STATUS_CONFIRMED, Rental::STATUS_ACTIVE])
                ->where('rentals.end_date', '>', Carbon::now())
                ->where('r2.end_date', '>', Carbon::now())
                ->whereNotNull('rentals.vehicle_id')
                ->whereRaw('rentals.start_date < r2.end_date AND rentals.end_date > r2.start_date')
                ->count();
        });
    }

    /**
     * Check logic for a specific conflict before confirming/creating.
     */
    public function checkVehicleAvailability(Vehicle $vehicle, $start, $end, $excludeRentalId = null): bool
    {
        // HARD RULE: Never allow rental activation if vehicle is in maintenance
        if ($vehicle->status === \App\Models\Vehicle::STATUS_MAINTENANCE) {
            return false;
        }

        // 1. Check existing confirmed/active rentals
        $rentalQuery = Rental::where('vehicle_id', $vehicle->id)
            ->whereIn('status', [Rental::STATUS_CONFIRMED, Rental::STATUS_ACTIVE])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_date', '<', $end)
                    ->where('end_date', '>', $start);
            });

        if ($excludeRentalId) {
            $rentalQuery->where('id', '!=', $excludeRentalId);
        }

        if ($rentalQuery->exists()) {
            return false; // Not available
        }

        // 2. Check Unavailabilities (Maintenance)
        $unavailabilityQuery = \App\Models\VehicleUnavailability::where('vehicle_id', $vehicle->id)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_datetime', '<', $end)
                    ->where('end_datetime', '>', $start);
            });

        if ($unavailabilityQuery->exists()) {
            return false; // Not available
        }

        return true; // Available
    }

    /**
     * Check logic for driver availability.
     */
    public function checkDriverAvailability(\App\Models\Driver $driver, $start, $end, $excludeRentalId = null): bool
    {
        // 1. Check status
        if ($driver->status !== \App\Models\Driver::STATUS_ACTIVE) {
            return false;
        }

        // 2. Check license expiry
        if ($driver->license_expiry_date < Carbon::today()) {
            return false;
        }

        // 3. Check existing confirmed/active rentals
        $rentalQuery = Rental::where('driver_id', $driver->id)
            ->whereIn('status', [Rental::STATUS_CONFIRMED, Rental::STATUS_ACTIVE])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_date', '<', $end)
                    ->where('end_date', '>', $start);
            });

        if ($excludeRentalId) {
            $rentalQuery->where('id', '!=', $excludeRentalId);
        }

        if ($rentalQuery->exists()) {
            return false; // Not available
        }

        return true;
    }
}
