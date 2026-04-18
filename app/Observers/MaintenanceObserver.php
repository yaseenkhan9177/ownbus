<?php

namespace App\Observers;

use App\Models\MaintenanceLog;

class MaintenanceObserver
{
    /**
     * Handle the MaintenanceLog "created" event.
     */
    public function created(MaintenanceLog $maintenanceLog): void
    {
        //
    }

    /**
     * Handle the MaintenanceLog "updated" event.
     */
    public function updated(MaintenanceLog $maintenanceLog): void
    {
        //
    }

    /**
     * Handle the MaintenanceLog "deleted" event.
     */
    public function deleted(MaintenanceLog $maintenanceLog): void
    {
        //
    }

    /**
     * Handle the MaintenanceLog "restored" event.
     */
    public function restored(MaintenanceLog $maintenanceLog): void
    {
        //
    }

    /**
     * Handle the MaintenanceLog "force deleted" event.
     */
    public function forceDeleted(MaintenanceLog $maintenanceLog): void
    {
        //
    }
}
