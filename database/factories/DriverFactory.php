<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'driver_code' => 'DRV-' . $this->faker->unique()->numerify('#####'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'national_id' => $this->faker->unique()->numerify('#####-#######-#'),
            'license_number' => $this->faker->unique()->bothify('??-####-####'),
            'license_expiry_date' => now()->addYears(2)->format('Y-m-d'),
            'license_type' => $this->faker->randomElement(['light', 'heavy', 'bus']),
            'hire_date' => now()->subMonths(rand(1, 48))->format('Y-m-d'),
            'salary' => $this->faker->randomFloat(2, 3000, 8000),
            'commission_rate' => $this->faker->randomFloat(2, 0, 15),
            'status' => Driver::STATUS_ACTIVE,
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'created_by' => function (array $attributes) {
                return \App\Models\User::factory()->create([
                    'company_id' => $attributes['company_id'] ?? \App\Models\Company::factory(),
                ])->id;
            },
        ];
    }
}
