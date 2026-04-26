<?php

namespace App\Repositories;

use App\Models\Vehicle;
use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VehicleRepository
{
    /**
     * Get paginated vehicles with filters and eager loading.
     */
    public function getVehicles(Company $company, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Vehicle::query();

        // Filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Eager Loading & Aggregates
        // Loading 'branch' if it exists (assuming Branch model exists)
        // Calculating total revenue via rentals relation
        $query->with(['branch', 'currentRental.customer'])
            ->withSum('rentals as total_revenue', 'final_amount');

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find a specific vehicle with detailed relationships.
     */
    public function findVehicle(Company $company, int $id): ?Vehicle
    {
        // Enforce scoping via ID in current tenant context
        $query = Vehicle::where('id', $id)
            ->with([
                'rentals' => function ($q) {
                    $q->latest()->limit(10); // Limit history for performance
                },
                'branch'
            ])
            ->withSum('rentals as total_revenue', 'final_amount');

        return $query->firstOrFail();
    }

    /**
     * Create a new vehicle.
     */
    public function createVehicle(Company $company, array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    /**
     * Update an existing vehicle.
     */
    public function updateVehicle(Vehicle $vehicle, array $data): bool
    {
        return $vehicle->update($data);
    }

    /**
     * Delete a vehicle.
     */
    public function deleteVehicle(Vehicle $vehicle): bool
    {
        return $vehicle->delete();
    }
}
