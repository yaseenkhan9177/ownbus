<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository
{
    /**
     * Get paginated customers with filters.
     */
    public function getCustomers(Company $company, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Customer::query()
            ->withCount(['rentals as active_rentals_count' => function ($q) {
                $q->whereIn('status', ['active', 'confirmed', 'dispatched']);
            }])
            ->withSum(['rentals as lifetime_revenue' => function ($q) {
                $q->whereIn('status', ['completed', 'closed']);
            }], 'final_amount');

        // Filter by Type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filter by Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Search by Name, Email, Phone, Code
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find customer with details.
     */
    public function findCustomer(Company $company, int $id): ?Customer
    {
        return Customer::where('id', $id)
            ->with(['rentals' => function ($q) {
                $q->latest()->limit(5);
            }])
            ->firstOrFail();
    }
}
