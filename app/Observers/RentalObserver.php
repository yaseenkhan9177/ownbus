<?php

namespace App\Observers;

use App\Models\Rental;
use App\Models\Vehicle;

class RentalObserver
{
    protected $balanceService;

    public function __construct(\App\Services\CustomerBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Handle the Rental "created" event.
     */
    public function created(Rental $rental): void
    {
        $this->updateVehicleStatus($rental);
        $this->updateCustomerBalance($rental);
    }

    /**
     * Handle the Rental "updating" event.
     */
    public function updating(Rental $rental): void
    {
        app(\App\Services\DataLockService::class)->checkLock($rental);
    }

    /**
     * Handle the Rental "updated" event.
     */
    public function updated(Rental $rental): void
    {
        if ($rental->wasChanged('status')) {
            $this->updateVehicleStatus($rental);
            $this->updateCustomerBalance($rental);

            // Driver availability is calculated dynamically based on active rentals.
            // No explicit 'busy' status column is updated as per architectural tip.
        }
    }

    /**
     * Handle the Rental "deleting" event.
     */
    public function deleting(Rental $rental): void
    {
        app(\App\Services\DataLockService::class)->checkLock($rental);
    }

    /**
     * Handle the Rental "deleted" event.
     */
    public function deleted(Rental $rental): void
    {
        if ($rental->vehicle) {
            $rental->vehicle->update(['status' => Vehicle::STATUS_AVAILABLE]);
        }
    }

    /**
     * Update the associated vehicle status dynamically.
     */
    protected function updateVehicleStatus(Rental $rental): void
    {
        if (!$rental->vehicle) {
            return;
        }

        if ($rental->status === Rental::STATUS_ACTIVE) {
            $rental->vehicle->update(['status' => Vehicle::STATUS_RENTED]);
        } elseif (in_array($rental->status, [Rental::STATUS_COMPLETED, Rental::STATUS_CANCELLED])) {
            // Only set back to available if there are no other active rentals for this vehicle
            // (Enterprise level protection)
            $isStillRented = Rental::where('vehicle_id', $rental->vehicle_id)
                ->where('id', '!=', $rental->id)
                ->where('status', Rental::STATUS_ACTIVE)
                ->exists();

            if (!$isStillRented) {
                $rental->vehicle->update(['status' => Vehicle::STATUS_AVAILABLE]);
            }
        }
    }

    /**
     * Update customer balance and record accounting when rental status changes.
     */
    protected function updateCustomerBalance(Rental $rental): void
    {
        if ($rental->wasChanged('status')) {
            $accounting = app(\App\Services\AccountingService::class);

            // 1. Rental Activation (Confirmed -> Active)
            if ($rental->status === Rental::STATUS_ACTIVE && $rental->getOriginal('status') === Rental::STATUS_CONFIRMED) {
                $accounting->recordRentalActivation($rental);

                // Legacy balance update
                if ($rental->customer) {
                    $this->balanceService->increaseBalance($rental->customer, (float) $rental->final_amount);
                }
            }

            // 2. Cancellation Reversal (Active -> Cancelled)
            if ($rental->status === Rental::STATUS_CANCELLED && $rental->getOriginal('status') === Rental::STATUS_ACTIVE) {
                // Find and reverse activation journal
                $journal = \App\Models\JournalEntry::where('reference_type', 'App\Models\Rental')
                    ->where('reference_id', $rental->id)
                    ->where('description', 'LIKE', 'Rental Activation%')
                    ->first();

                if ($journal) {
                    $accounting->reverseEntry($journal, "Rental #{$rental->rental_number} cancelled after activation.");
                }
            }
        }
    }
}
