<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_number' => $this->faker->unique()->bothify('ABC-####'),
            'name' => 'Test Vehicle ' . $this->faker->word,
            'make' => $this->faker->word,
            'model' => $this->faker->word,
            'year' => $this->faker->year,
            'type' => $this->faker->randomElement(['bus', 'van', 'suv']),
            'status' => 'available',
            'daily_rate' => $this->faker->randomFloat(2, 500, 2000),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
