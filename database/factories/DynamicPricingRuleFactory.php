<?php

namespace Database\Factories;

use App\Models\DynamicPricingRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class DynamicPricingRuleFactory extends Factory
{
    protected $model = DynamicPricingRule::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'name' => 'Seasonal Surge',
            'rule_type' => 'seasonal',
            'conditions' => [
                'start_date' => now()->startOfYear()->toDateString(),
                'end_date' => now()->endOfYear()->toDateString(),
            ],
            'adjustment_type' => 'percentage',
            'adjustment_value' => 10.0,
            'priority' => 10,
            'is_active' => true,
        ];
    }
}
