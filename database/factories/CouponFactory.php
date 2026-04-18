<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'code' => $this->faker->unique()->bothify('Promo???###'),
            'name' => 'Test Coupon',
            'type' => 'percentage',
            'value' => 10.0,
            'is_active' => true,
        ];
    }
}
