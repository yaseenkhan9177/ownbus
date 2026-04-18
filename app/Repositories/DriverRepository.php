<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Driver;
use App\Models\Rental;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DriverRepository
{
    /**
     * Get paginated drivers with filters and eager loading.
     */
    public function getDrivers(Company $company, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Driver::query()
            ->with(['branch']);

        // Filter by Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Search by Name, Code or Phone
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('driver_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by License Expiry (upcoming)
        if (!empty($filters['license_expiring_soon'])) {
            $query->where('license_expiry_date', '<=', now()->addDays(30))
                ->where('license_expiry_date', '>', now());
        }

        // Filter by License Expired
        if (!empty($filters['license_expired'])) {
            $query->where('license_expiry_date', '<', now());
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find a driver by ID with detailed info.
     */
    public function findDriver(Company $company, int $id): ?Driver
    {
        return Driver::where('id', $id)
            ->with([
                'branch',
                'creator',
            ])
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * Get driver's rental history.
     */
    public function getDriverRentals(Driver $driver, int $limit = 10)
    {
        return Rental::where('driver_id', $driver->id)
            ->with(['customer', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }
}
