<?php

namespace App\Observers;

use App\Models\Contract;
use App\Services\DataLockService;

class ContractObserver
{
    /**
     * Handle the Contract "updating" event.
     */
    public function updating(Contract $contract): void
    {
        app(DataLockService::class)->checkLock($contract);
    }

    /**
     * Handle the Contract "deleting" event.
     */
    public function deleting(Contract $contract): void
    {
        app(DataLockService::class)->checkLock($contract);
    }
}
