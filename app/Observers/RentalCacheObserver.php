<?php

namespace App\Observers;

use App\Models\Rental;
use App\Services\CacheService;

/**
 * Observer to automatically invalidate cache when rentals change
 */
class RentalCacheObserver
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Rental "created" event.
     */
    public function created(Rental $rental): void
    {
        $this->invalidateCache($rental);
    }

    /**
     * Handle the Rental "updated" event.
     */
    public function updated(Rental $rental): void
    {
        $this->invalidateCache($rental);
    }

    /**
     * Handle the Rental "deleted" event.
     */
    public function deleted(Rental $rental): void
    {
        $this->invalidateCache($rental);
    }

    /**
     * Invalidate relevant caches
     */
    protected function invalidateCache(Rental $rental): void
    {
        if ($rental->company_id) {
            // Rentals affect dashboard KPIs and fleet availability
            $this->cacheService->invalidateDashboardKPIs($rental->company_id);
            $this->cacheService->invalidateFleetAvailability($rental->company_id);
        }
    }
}
