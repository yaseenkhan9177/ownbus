<?php

namespace App\Observers;

use App\Models\Vehicle;
use App\Services\CacheService;

/**
 * Observer to automatically invalidate cache when vehicles change
 */
class VehicleCacheObserver
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Vehicle "created" event.
     */
    public function created(Vehicle $vehicle): void
    {
        $this->invalidateCache($vehicle);
    }

    /**
     * Handle the Vehicle "updated" event.
     */
    public function updated(Vehicle $vehicle): void
    {
        $this->invalidateCache($vehicle);
    }

    /**
     * Handle the Vehicle "deleted" event.
     */
    public function deleted(Vehicle $vehicle): void
    {
        $this->invalidateCache($vehicle);
    }

    /**
     * Invalidate relevant caches
     */
    protected function invalidateCache(Vehicle $vehicle): void
    {
        // Get company_id - check if vehicle has it directly or via relation
        $companyId = $vehicle->company_id ?? null;

        if ($companyId) {
            $this->cacheService->invalidateFleetAvailability($companyId, $vehicle->type);
            $this->cacheService->invalidateDashboardKPIs($companyId);
            // Also invalidate quota status as vehicle count affects quotas
            $this->cacheService->invalidateSubscription($companyId);
        }
    }
}
