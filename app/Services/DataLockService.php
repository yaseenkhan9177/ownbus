<?php

namespace App\Services;

use App\Models\AccountingPeriod;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\Rental;
use App\Models\VehicleFine;
use App\Models\User;
use App\Exceptions\DataLockedException;
use App\Services\EventLoggerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DataLockService
{
    protected $logger;

    public function __construct(EventLoggerService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Check if a model is locked and throw an exception if so (unless overridden).
     *
     * @param Model $model
     * @param User|null $user
     * @throws DataLockedException
     */
    public function checkLock(Model $model, ?User $user = null): void
    {
        $user = $user ?? Auth::user();

        if (!$this->isLockedByStatusOrPeriod($model)) {
            return;
        }

        // ── Override Logic ────────────────────────────────────────────────
        if ($user && $user->can('override_data_lock')) {
            $this->logOverride($model, $user);
            return;
        }

        throw new DataLockedException($this->lockReason($model));
    }

    /**
     * Determine if a model is locked based purely on business rules.
     */
    public function isLocked(Model $model, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if ($user && $user->can('override_data_lock')) {
            return false;
        }

        return $this->isLockedByStatusOrPeriod($model);
    }

    /**
     * Internal status/period check logic.
     */
    protected function isLockedByStatusOrPeriod(Model $model): bool
    {
        // 1. Rental Locks
        if ($model instanceof Rental && $model->status === Rental::STATUS_COMPLETED) {
            return true;
        }

        // 2. Fine Locks
        if ($model instanceof VehicleFine && $model->status === VehicleFine::STATUS_PAID) {
            return true;
        }

        // 3. Expense Locks
        if ($model instanceof Expense && $model->is_posted) {
            return true;
        }

        // 4. Contract Locks
        if ($model instanceof Contract && $model->status === Contract::STATUS_EXPIRED) {
            return true;
        }

        // ── Accounting Period Lock ────────────────────────────────────────
        return $this->isInsideClosedPeriod($model);
    }

    /**
     * Get the human-readable reason for the lock.
     */
    public function lockReason(Model $model): ?string
    {
        if ($model instanceof Rental && $model->status === Rental::STATUS_COMPLETED) {
            return "Rental already completed and finalized";
        }

        if ($model instanceof VehicleFine && $model->status === VehicleFine::STATUS_PAID) {
            return "Vehicle fine has already been paid and settled";
        }

        if ($model instanceof Expense && $model->is_posted) {
            return "Expense has been posted to general ledger journals";
        }

        if ($model instanceof Contract && $model->status === Contract::STATUS_EXPIRED) {
            return "Contract term has expired and record is closed";
        }

        if ($this->isInsideClosedPeriod($model)) {
            return "Financial accounting period for this date is closed";
        }

        return null;
    }

    /**
     * Log a lock override to the audit trail.
     */
    protected function logOverride(Model $model, User $user): void
    {
        $this->logger->log(
            company: $user->company ?? Auth::user()->company,
            eventType: 'data_lock_overridden',
            entity: $model,
            title: 'Data Lock Overridden',
            severity: 'warning',
            meta: [
                'entity_type' => class_basename($model),
                'entity_id' => $model->id,
                'user_name' => $user->name,
                'override_reason' => 'Authorized administrative bypass requested'
            ],
            user: $user
        );
    }

    /**
     * Check if the model's date falls within a closed period.
     */
    protected function isInsideClosedPeriod(Model $model): bool
    {
        $date = $this->getModelDate($model);
        if (!$date) return false;

        return AccountingPeriod::where('is_closed', true)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }

    protected function getModelDate(Model $model)
    {
        if ($model instanceof Rental) return $model->actual_return_date ?? $model->end_date;
        if ($model instanceof VehicleFine) return $model->fine_date;
        if ($model instanceof Expense) return $model->expense_date;
        if ($model instanceof Contract) return $model->end_date;

        return $model->created_at;
    }
}
