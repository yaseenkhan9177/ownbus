<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\RentalStatusLog;
use App\Exceptions\InvalidStateTransitionException;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class RentalStateService
{
    /**
     * Allowed transitions for the enterprise rental state machine.
     */
    protected const TRANSITIONS = [
        Rental::STATUS_DRAFT => [Rental::STATUS_CONFIRMED, Rental::STATUS_CANCELLED],
        Rental::STATUS_CONFIRMED => [Rental::STATUS_ACTIVE, Rental::STATUS_CANCELLED, Rental::STATUS_DRAFT],
        Rental::STATUS_ACTIVE => [Rental::STATUS_COMPLETED, Rental::STATUS_CANCELLED],
        Rental::STATUS_COMPLETED => [], // Terminal state
        Rental::STATUS_CANCELLED => [Rental::STATUS_DRAFT], // Allow reviving a cancelled rental as draft
    ];

    /**
     * Change the status of a rental.
     *
     * @param Rental $rental
     * @param string $toStatus
     * @param string|null $reason
     * @return Rental
     * @throws Exception
     */
    public function transition(Rental $rental, string $toStatus, ?string $reason = null): Rental
    {
        $fromStatus = $rental->status;

        // 1. Validate Transition
        if (!$this->canTransition($fromStatus, $toStatus)) {
            throw new InvalidStateTransitionException($fromStatus, $toStatus);
        }

        // 2. Business Logic Checks
        if ($toStatus === Rental::STATUS_CONFIRMED) {
            if (empty($rental->vehicle_id)) {
                throw new Exception("Cannot confirm rental #{$rental->rental_number} without an assigned vehicle.");
            }
        }

        if ($toStatus === Rental::STATUS_ACTIVE) {
            if (empty($rental->vehicle_id)) {
                throw new Exception("Cannot activate rental #{$rental->rental_number} without an assigned vehicle.");
            }
            // Driver might be optional based on user business model, but usually good to have.
        }

        // 3. Execute Transaction
        return DB::transaction(function () use ($rental, $fromStatus, $toStatus, $reason) {
            // Update Rental Status
            $rental->status = $toStatus;

            // Handle specific status logic
            $this->handleStatusHooks($rental, $toStatus);

            $rental->save();

            // Log Status Change
            RentalStatusLog::create([
                'rental_id' => $rental->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'changed_by' => Auth::id(),
                'reason' => $reason,
                'created_at' => now(),
            ]);

            return $rental;
        });
    }

    public function canTransition(string $from, string $to): bool
    {
        $allowed = self::TRANSITIONS[$from] ?? [];
        return in_array($to, $allowed);
    }

    protected function handleStatusHooks(Rental $rental, string $toStatus)
    {
        if ($toStatus === Rental::STATUS_ACTIVE) {
            if (!$rental->actual_start_datetime) {
                $rental->actual_start_datetime = now();
            }
        }

        if ($toStatus === Rental::STATUS_COMPLETED) {
            if (!$rental->actual_return_date) {
                $rental->actual_return_date = now();
            }
        }
    }
}
