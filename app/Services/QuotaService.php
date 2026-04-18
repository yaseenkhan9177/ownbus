<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Branch;
use App\Models\UsageMetric;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QuotaService
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Check if a company can create a new vehicle.
     * 
     * @param Company $company
     * @return bool
     */
    public function canCreateVehicle(Company $company): bool
    {
        $plan = $this->subscriptionService->getCurrentPlan($company);

        if (!$plan) {
            return false;
        }

        $currentCount = Vehicle::count();
        $limit = $plan->getLimit('vehicles');

        return $limit === null || $currentCount < $limit;
    }

    /**
     * Check if a company can create a new user.
     * 
     * @param Company $company
     * @return bool
     */
    public function canCreateUser(Company $company): bool
    {
        $plan = $this->subscriptionService->getCurrentPlan($company);

        if (!$plan) {
            return false;
        }

        $currentCount = User::count();
        $limit = $plan->getLimit('users');

        return $limit === null || $currentCount < $limit;
    }

    /**
     * Check if a company can create a new branch.
     * 
     * @param Company $company
     * @return bool
     */
    public function canCreateBranch(Company $company): bool
    {
        $plan = $this->subscriptionService->getCurrentPlan($company);

        if (!$plan) {
            return false;
        }

        $currentCount = Branch::count();
        $limit = $plan->getLimit('branches');

        return $limit === null || $currentCount < $limit;
    }

    /**
     * Get current usage for a specific resource.
     * 
     * @param Company $company
     * @param string $resourceType
     * @return int
     */
    public function getCurrentUsage(Company $company, string $resourceType): int
    {
        return match ($resourceType) {
            'vehicles' => Vehicle::count(),
            'users' => User::count(),
            'branches' => Branch::count(),
            default => 0,
        };
    }

    /**
     * Get the limit for a specific resource.
     * 
     * @param Company $company
     * @param string $resourceType
     * @return int|null
     */
    public function getLimit(Company $company, string $resourceType): ?int
    {
        $plan = $this->subscriptionService->getCurrentPlan($company);

        if (!$plan) {
            return 0;
        }

        return $plan->getLimit($resourceType);
    }

    /**
     * Get usage percentage for a resource.
     * 
     * @param Company $company
     * @param string $resourceType
     * @return float
     */
    public function getUsagePercentage(Company $company, string $resourceType): float
    {
        $current = $this->getCurrentUsage($company, $resourceType);
        $limit = $this->getLimit($company, $resourceType);

        if (!$limit) {
            return 0;
        }

        return ($current / $limit) * 100;
    }

    /**
     * Sync all usage metrics for a company (snapshot).
     * 
     * @param Company $company
     * @return void
     */
    public function syncUsageMetrics(Company $company): void
    {
        $metrics = ['vehicles', 'users', 'branches'];
        $timestamp = Carbon::now();

        foreach ($metrics as $metric) {
            UsageMetric::create([
                'company_id' => $company->id,
                'metric_type' => $metric,
                'current_count' => $this->getCurrentUsage($company, $metric),
                'recorded_at' => $timestamp,
            ]);
        }
    }

    /**
     * Get quota status for all resources.
     * 
     * @param Company $company
     * @return array
     */
    public function getQuotaStatus(Company $company): array
    {
        $resources = ['vehicles', 'users', 'branches'];
        $status = [];

        foreach ($resources as $resource) {
            $status[$resource] = [
                'current' => $this->getCurrentUsage($company, $resource),
                'limit' => $this->getLimit($company, $resource),
                'percentage' => $this->getUsagePercentage($company, $resource),
                'can_create' => $this->{"canCreate" . ucfirst(Str::singular($resource))}($company),
            ];
        }

        return $status;
    }
}
