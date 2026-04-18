<?php

namespace App\Observers;

use App\Models\Vendor;
use App\Services\VendorService;

class VendorObserver
{
    protected VendorService $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * After a vendor is created, post opening balance journal entry if applicable.
     */
    public function created(Vendor $vendor): void
    {
        if ($vendor->opening_balance > 0 && $vendor->balance_direction) {
            $this->vendorService->createOpeningBalanceEntry($vendor);
        }
    }
}
