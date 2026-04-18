<?php

namespace App\Services\Intelligence;

use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\SeasonalPricingRule;
use App\Models\PricingDecision;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PricingEngineService
{
    /**
     * Calculate optimal rental rate based on multiple AI signals.
     */
    public function calculateRate(
        Vehicle $vehicle,
        Branch $branch,
        Customer $customer,
        Carbon $startDate
    ): array {
        $baseRate = $vehicle->daily_rate ?: 1000; // Fallback to standard if daily not set

        $utilMultiplier = $this->getUtilizationMultiplier($branch);
        $seasonMultiplier = $this->getSeasonalityMultiplier($startDate, $branch);
        $urgencyMultiplier = $this->getUrgencyMultiplier($startDate);
        $riskMultiplier = $this->getRiskMultiplier($customer);
        $categoryMultiplier = $this->getCategoryMultiplier($vehicle);

        $optimizedRate = $baseRate
            * $utilMultiplier
            * $seasonMultiplier
            * $urgencyMultiplier
            * $riskMultiplier
            * $categoryMultiplier;

        $breakdown = [
            'utilization_multiplier' => round($utilMultiplier, 2),
            'season_multiplier' => round($seasonMultiplier, 2),
            'urgency_multiplier' => round($urgencyMultiplier, 2),
            'risk_multiplier' => round($riskMultiplier, 2),
            'category_multiplier' => round($categoryMultiplier, 2),
        ];

        return [
            'base_rate' => round($baseRate, 2),
            'optimized_rate' => round($optimizedRate, 2),
            'breakdown' => $breakdown
        ];
    }

    /**
     * Multiply rates based on real-time fleet pressure.
     * Refresh utilization cache hourly.
     */
    protected function getUtilizationMultiplier(Branch $branch): float
    {
        $cacheKey = "branch:{$branch->id}:utilization_percent";
        $utilization = Cache::remember($cacheKey, 3600, function () use ($branch) {
            $totalVehicles = Vehicle::where('branch_id', $branch->id)->count();
            if ($totalVehicles === 0) return 0;

            $rentedVehicles = Vehicle::where('branch_id', $branch->id)
                ->where('status', Vehicle::STATUS_RENTED)
                ->count();

            return ($rentedVehicles / $totalVehicles) * 100;
        });

        // Precision Pricing Tiers
        if ($utilization >= 90) return 1.25; // Critical shortage
        if ($utilization >= 80) return 1.15; // High pressure
        if ($utilization >= 60) return 1.05; // Healthy
        if ($utilization < 40) return 0.90;  // Oversupply - discount to move fleet
        if ($utilization < 20) return 0.85;  // Extreme surplus

        return 1.0;
    }

    /**
     * Fetch seasonal multipliers from the config table.
     */
    protected function getSeasonalityMultiplier(Carbon $date, Branch $branch): float
    {
        $rule = SeasonalPricingRule::where('is_active', true)
            ->where(function ($q) use ($branch) {
                $q->where('branch_id', $branch->id)
                    ->orWhereNull('branch_id');
            })
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->orderByDesc('branch_id') // Specific branch rules override global ones
            ->first();

        return $rule ? (float) $rule->multiplier : 1.0;
    }

    /**
     * Booking urgency impact.
     * Immediate / Last-minute bookings incur surcharges.
     */
    protected function getUrgencyMultiplier(Carbon $startDate): float
    {
        $daysLeadTime = now()->diffInDays($startDate, false);

        if ($daysLeadTime <= 0) return 1.20; // Immediate / Emergency booking
        if ($daysLeadTime <= 2) return 1.10; // High urgency
        if ($daysLeadTime <= 5) return 1.05; // Moderate urgency
        if ($daysLeadTime >= 21) return 0.90; // Strategic early bird discount
        if ($daysLeadTime >= 10) return 0.95; // Standard advance discount

        return 1.0;
    }

    /**
     * Customer Risk Multiplier.
     * Loyal and safe customers get discounts; risky ones pay premium.
     */
    protected function getRiskMultiplier(Customer $customer): float
    {
        // Near credit limit or manual block -> risk surcharge
        if ($customer->isCreditBlocked() || $customer->current_balance > ($customer->credit_limit * 0.9)) {
            return 1.12;
        }

        // Low risk + active status -> loyalty incentive
        if ($customer->status === Customer::STATUS_ACTIVE && $customer->current_balance < ($customer->credit_limit * 0.3)) {
            return 0.95;
        }

        return 1.0;
    }

    /**
     * Premium categorization for specialized fleet.
     */
    protected function getCategoryMultiplier(Vehicle $vehicle): float
    {
        return match (strtolower($vehicle->type)) {
            'luxury', 'vip', 'premium', 'executive' => 1.15,
            'standard', 'economy', 'budget' => 1.0,
            'bus-large', 'coach' => 1.05,
            'van', 'minibus' => 1.02,
            default => 1.0,
        };
    }

    /**
     * Log the pricing calculation for audit and AI training.
     */
    public function logDecision(array $data): void
    {
        PricingDecision::create($data);
    }
}
