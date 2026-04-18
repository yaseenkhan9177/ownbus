<?php

namespace App\Observers;

use App\Models\VehicleFine;
use App\Services\Fines\FineRecoveryService;
use Illuminate\Support\Facades\Log;

/**
 * Vehicle Fine Observer
 *
 * Triggers the fine auto-recovery engine when a fine status changes to "paid".
 *
 * Business logic:
 *  1. Company records fine (status = pending)
 *  2. Company pays fine to traffic authority (status → paid)
 *  3. THIS OBSERVER FIRES: if customer_responsible = true
 *     → processCustomerRecovery() posts DR AR / CR Fine Recovery Income
 *
 * Why updated() not created()?
 *  Because we need the company to have PAID first. Recovery happens AFTER
 *  the company has incurred the expense, not at recording time.
 */
class VehicleFineObserver
{
    protected FineRecoveryService $recovery;

    public function __construct(FineRecoveryService $recovery)
    {
        $this->recovery = $recovery;
    }

    /**
     * Handle fine update protection.
     */
    public function updating(VehicleFine $fine): void
    {
        app(\App\Services\DataLockService::class)->checkLock($fine);
    }

    /**
     * Handle fine deletion protection.
     */
    public function deleting(VehicleFine $fine): void
    {
        app(\App\Services\DataLockService::class)->checkLock($fine);
    }

    /**
     * Handle fine status transitions.
     * Only fires recovery when status transitions TO 'paid'.
     */
    public function updated(VehicleFine $fine): void
    {
        // Only react to status field changes
        if (!$fine->isDirty('status')) {
            return;
        }

        // Trigger: status just became 'paid'
        if ($fine->status === 'paid' && $fine->customer_responsible) {
            Log::info("VehicleFineObserver: Fine #{$fine->fine_number} marked paid + customer_responsible — triggering recovery.");

            try {
                $this->recovery->processCustomerRecovery($fine);
            } catch (\Throwable $e) {
                // Log but don't throw — don't rollback the fine payment because of recovery failure
                Log::error("VehicleFineObserver: Recovery failed for fine #{$fine->fine_number} — {$e->getMessage()}");
            }
        }
    }

    /**
     * Log when a fine is created for audit trail.
     */
    public function created(VehicleFine $fine): void
    {
        Log::info(sprintf(
            'VehicleFine created: #%s | Vehicle: %s | Amount: AED %s | Customer responsible: %s',
            $fine->fine_number,
            $fine->vehicle_id,
            $fine->amount,
            $fine->customer_responsible ? 'Yes' : 'No'
        ));
    }
}
