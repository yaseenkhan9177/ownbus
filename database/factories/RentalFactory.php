<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'company_id' => \App\Models\Company::factory(),
            'branch_id' => null,
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'driver_id' => \App\Models\User::factory()->create(['role' => 'driver'])->id,
            'rental_type' => 'daily',
            'rental_number' => 'RENT-' . $this->faker->unique()->bothify('??#####'),
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(4),
            'status' => 'confirmed',
            'final_amount' => $this->faker->randomFloat(2, 1000, 5000),
            'tax' => $this->faker->randomFloat(2, 50, 200),
            'pickup_location' => $this->faker->city,
            'dropoff_location' => $this->faker->city,
        ];
    }
}
