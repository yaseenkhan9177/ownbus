<?php

namespace Database\Factories;

use App\Models\BusProfitabilityMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusProfitabilityMetricFactory extends Factory
{
    protected $model = BusProfitabilityMetric::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'month_year' => now()->format('Y-m'),
            'total_revenue' => 1000.00,
            'fuel_cost' => 200.00,
            'maintenance_cost' => 100.00,
            'net_profit' => 700.00,
            'days_rented' => 15,
            'total_km' => 3000,
        ];
    }
}
