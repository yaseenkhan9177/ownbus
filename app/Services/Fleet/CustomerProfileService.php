<?php

namespace App\Services\Fleet;

use App\Models\Customer;
use Carbon\Carbon;

class CustomerProfileService
{
    /**
     * Calculate comprehensive customer metrics.
     */
    public function getCustomerMetrics(Customer $customer): array
    {
        // Aggregate fully via DB for performance
        $rentals = $customer->rentals;

        $lifetimeRevenue = $rentals->whereIn('status', ['completed', 'closed'])->sum('final_amount');
        $totalRentals = $rentals->count();
        $activeRentals = $rentals->whereIn('status', ['active', 'confirmed', 'dispatched'])->count();
        $overdueRentals = $rentals->where('status', 'active')
            ->filter(function ($rental) {
                return $rental->end_date && $rental->end_date->isPast();
            })->count();

        $lastRental = $rentals->sortByDesc('start_date')->first();
        $lastRentalDate = $lastRental ? $lastRental->start_date : null;

        return [
            'lifetime_revenue' => $lifetimeRevenue,
            'total_rentals' => $totalRentals,
            'active_rentals' => $activeRentals,
            'overdue_rentals_count' => $overdueRentals,
            'last_rental_date' => $lastRentalDate,
            'average_rental_value' => $totalRentals > 0 ? round($lifetimeRevenue / $totalRentals, 2) : 0,
        ];
    }
}
