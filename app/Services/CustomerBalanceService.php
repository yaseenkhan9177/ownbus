<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerBalanceService
{
    /**
     * Increase customer balance (e.g., when rental is activated)
     */
    public function increaseBalance(Customer $customer, float $amount): void
    {
        if ($amount <= 0) return;

        DB::transaction(function () use ($customer, $amount) {
            $customer->increment('current_balance', $amount);
        });
    }

    /**
     * Decrease customer balance (e.g., when payment is received)
     */
    public function decreaseBalance(Customer $customer, float $amount): void
    {
        if ($amount <= 0) return;

        DB::transaction(function () use ($customer, $amount) {
            $customer->decrement('current_balance', $amount);
        });
    }

    /**
     * Check if customer can afford an additional amount based on credit limit
     */
    public function canAfford(Customer $customer, float $additionalAmount): bool
    {
        // Rule: If credit_limit is 0, they are Cash-Only (no credit allowed)
        if ($customer->credit_limit <= 0) {
            return false;
        }

        return ($customer->current_balance + $additionalAmount) <= $customer->credit_limit;
    }
}
