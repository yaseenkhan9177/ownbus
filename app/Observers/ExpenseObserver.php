<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\DataLockService;

class ExpenseObserver
{
    /**
     * Handle the Expense "updating" event.
     */
    public function updating(Expense $expense): void
    {
        app(DataLockService::class)->checkLock($expense);
    }

    /**
     * Handle the Expense "deleting" event.
     */
    public function deleting(Expense $expense): void
    {
        app(DataLockService::class)->checkLock($expense);
    }
}
