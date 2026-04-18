<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create "50-Seater Luxury" Buses (5 Units)
        for ($i = 101; $i <= 105; $i++) {
            \App\Models\Vehicle::create([
                'vehicle_number' => 'DXB-' . $i,
                'name' => 'Mercedes Tourismo ' . $i,
                'make' => 'Mercedes-Benz',
                'model' => 'Tourismo',
                'year' => 2024,
                'status' => 'active',
                'purchase_price' => 850000,
                'purchase_date' => now()->subMonths(rand(1, 12)),
                'current_odometer' => rand(1000, 50000),
                'type' => '50-Seater Luxury',
                'daily_rate' => 1500.00,
            ]);
        }

        // 2. Create "30-Seater Coaster" Buses (3 Units)
        for ($i = 201; $i <= 203; $i++) {
            \App\Models\Vehicle::create([
                'vehicle_number' => 'SHJ-' . $i,
                'name' => 'Toyota Coaster ' . $i,
                'make' => 'Toyota',
                'model' => 'Coaster',
                'year' => 2023,
                'status' => 'active',
                'purchase_price' => 220000,
                'purchase_date' => now()->subMonths(rand(6, 24)),
                'current_odometer' => rand(50000, 150000),
                'type' => '30-Seater Coaster',
                'daily_rate' => 800.00,
            ]);
        }

        // 3. Create "14-Seater Hiace" (2 Units)
        for ($i = 301; $i <= 302; $i++) {
            \App\Models\Vehicle::create([
                'vehicle_number' => 'AUH-' . $i,
                'name' => 'Toyota Hiace ' . $i,
                'make' => 'Toyota',
                'model' => 'Hiace',
                'year' => 2025,
                'status' => 'active',
                'purchase_price' => 110000,
                'purchase_date' => now()->subMonths(2),
                'current_odometer' => rand(500, 5000),
                'type' => '14-Seater Hiace',
                'daily_rate' => 400.00,
            ]);
        }
    }
}
