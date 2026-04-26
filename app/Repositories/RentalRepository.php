<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Rental;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class RentalRepository
{
    /**
     * Get paginated rentals with filters and eager loading.
     */
    public function getRentals(Company $company, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Rental::query()
            ->with(['customer', 'vehicle', 'driver']);

        // Filter by Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by Branch
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        // Filter by Date Range
        if (!empty($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('end_date', '<=', $filters['date_to']);
        }

        // Search by Rental Number or Customer Name
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('rental_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Search by Vehicle
        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        // Sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        // Map sort field if needed
        if ($sortField === 'start_datetime') $sortField = 'start_date';
        if ($sortField === 'end_datetime') $sortField = 'end_date';

        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find a rental by ID with comprehensive eager loading.
     */
    public function findRental(Company $company, int $id): ?Rental
    {
        $rental = Rental::where('id', $id)
            ->with([
                'items',
                'customer',
                'vehicle',
                'driver',
                'statusLogs' => function ($q) {
                    $q->latest();
                },
                'transactions'
            ])
            ->firstOrFail();

        // Load creator manually (central DB)
        if ($rental->created_by) {
            $rental->setRelation('creator', \App\Models\User::find($rental->created_by));
        }

        // Load status log users manually (central DB)
        $rental->statusLogs->each(function ($log) {
            if ($log->changed_by) {
                $log->setRelation('user', \App\Models\User::find($log->changed_by));
            }
        });

        return $rental;
    }

    /**
     * Create a new rental.
     */
    public function createRental(Company $company, array $data): Rental
    {

        if (empty($data['uuid'])) {
            $data['uuid'] = (string) Str::uuid();
        }

        return Rental::create($data);
    }

    /**
     * Update an existing rental.
     */
    public function updateRental(Rental $rental, array $data): bool
    {
        return $rental->update($data);
    }

    /**
     * Delete a rental.
     */
    public function deleteRental(Rental $rental): bool
    {
        return $rental->delete();
    }
}
