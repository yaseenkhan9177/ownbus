<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GpsDevice>
 */
class GpsDeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'imei_number' => $this->faker->unique()->numerify('###############'),
            'provider' => 'teltonika',
            'status' => 'active',
        ];
    }
}
