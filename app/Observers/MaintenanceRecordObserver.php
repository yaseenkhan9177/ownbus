<?php

namespace App\Observers;

use App\Models\MaintenanceRecord;
use App\Models\Vehicle;

class MaintenanceRecordObserver
{
    /**
     * Handle the MaintenanceRecord "updated" event.
     */
    public function updated(MaintenanceRecord $record): void
    {
        if ($record->isDirty('status')) {
            $vehicle = $record->vehicle;

            // 1. Lock/Unlock Vehicle
            if ($record->status === MaintenanceRecord::STATUS_IN_PROGRESS) {
                // Lock vehicle
                $vehicle->update(['status' => Vehicle::STATUS_MAINTENANCE]);
            } elseif (in_array($record->status, [MaintenanceRecord::STATUS_COMPLETED, MaintenanceRecord::STATUS_CANCELLED])) {
                // Return to available only if there are no other active maintenance jobs
                $hasActive = MaintenanceRecord::where('vehicle_id', $vehicle->id)
                    ->where('status', MaintenanceRecord::STATUS_IN_PROGRESS)
                    ->where('id', '!=', $record->id)
                    ->exists();

                if (!$hasActive) {
                    $vehicle->update(['status' => Vehicle::STATUS_AVAILABLE]);
                }
            }

            // 2. ERP Accounting: Record Expense on Completion
            if ($record->status === MaintenanceRecord::STATUS_COMPLETED) {
                app(\App\Services\AccountingService::class)->recordMaintenanceCost($record);
            }
        }
    }

    /**
     * Handle the MaintenanceRecord "created" event.
     */
    public function created(MaintenanceRecord $record): void
    {
        if ($record->status === MaintenanceRecord::STATUS_IN_PROGRESS) {
            $record->vehicle->update(['status' => Vehicle::STATUS_MAINTENANCE]);
        }
    }
}
