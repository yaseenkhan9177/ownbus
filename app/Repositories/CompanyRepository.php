<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
    /**
     * Get paginated list of companies for Super Admin with advanced filtering.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedAdminList(array $filters = [], int $perPage = 15)
    {
        $query = Company::withoutGlobalScopes()
            ->select('companies.*', 'total_vehicles as vehicles_count')
            ->with(['owner', 'subscription.plan']);

        // Apply string-based search (name, owner name, email)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                        $ownerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply exact status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply plan filter via nested subscription querying
        if (!empty($filters['plan_id'])) {
            $query->whereHas('subscription', function ($subQuery) use ($filters) {
                $subQuery->where('plan_id', $filters['plan_id']);
            });
        }

        // Apply explicit date range filtering (created_at)
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Fetch a deeply nested company payload for the Admin Detail view.
     *
     * @param int|string $id
     * @return Company
     */
    public function getAdminDetails($id)
    {
        $company = Company::withoutGlobalScopes()
            ->with([
                'owner',
                'subscription.plan',
                'subscription.invoices' => function ($q) {
                    $q->latest()->limit(20);
                },
                'subscription.events' => function ($q) {
                    $q->latest()->limit(50);
                },
                'users' => function ($q) {
                    $q->with('roles');
                }
            ])
            ->withCount(['users'])
            ->findOrFail($id);

        // Fetch cross-database counts manually if tenant database is set
        if (!empty($company->database_name)) {
            try {
                \App\Services\TenantService::switchDatabase($company->database_name);
                $company->vehicles_count = \App\Models\Vehicle::count();
                $company->branches_count = \App\Models\Branch::count();
            } catch (\Exception $e) {
                // Fallback if tenant DB is not accessible
                $company->vehicles_count = $company->total_vehicles ?? 0;
                $company->branches_count = 0;
            }
        } else {
            $company->vehicles_count = $company->total_vehicles ?? 0;
            $company->branches_count = 0;
        }

        return $company;
    }
}
