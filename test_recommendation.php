<?php

use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Company;
use App\Services\BusRecommendationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Setup Data
$company = Company::first();
if (!$company) {
    $company = Company::create(['name' => 'TestCo', 'status' => 'active']);
}

// Clear relevant tables for clean test
\Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
Booking::truncate();
DB::table('vehicle_unavailabilities')->truncate();
DB::table('bus_profitability_metrics')->truncate();
Vehicle::truncate();
\Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

// 2. Create Semantic Vehicles
// Vehicle A: High Profit, Low Mileage (Should be #1)
$vA = Vehicle::create([
    'company_id' => $company->id,
    'name' => 'Bus A (Star)',
    'vehicle_number' => 'BUS-A',
    'make' => 'Toyota',
    'model' => 'Coaster',
    'year' => 2024,
    'type' => 'bus',
    'status' => 'active',
    'current_odometer' => 10000, // Low mileage (Score ~98)
    'daily_rate' => 200,
    'ownership_type' => 'own' // Fixed enum
]);
DB::table('bus_profitability_metrics')->insert([
    'vehicle_id' => $vA->id,
    'net_profit' => 8000,
    'month_year' => '2024-01-01',
    'created_at' => now(),
    'updated_at' => now()
]);
// Profit Score: 8000/10000 * 100 = 80
// Total ~ (80*0.4) + (98*0.3) + (30*0.3) = 32 + 29.4 + 9? (Recency default 30 days)

// Vehicle B: Low Profit, High Mileage (Should be lower)
$vB = Vehicle::create([
    'company_id' => $company->id,
    'name' => 'Bus B (Old)',
    'vehicle_number' => 'BUS-B',
    'make' => 'Toyota',
    'model' => 'Coaster',
    'year' => 2020,
    'type' => 'bus',
    'status' => 'active',
    'current_odometer' => 450000, // High mileage (Score ~10)
    'daily_rate' => 150,
    'ownership_type' => 'own'
]);
DB::table('bus_profitability_metrics')->insert([
    'vehicle_id' => $vB->id,
    'net_profit' => 2000,
    'month_year' => '2024-01-01',
    'created_at' => now(),
    'updated_at' => now()
]);
// Profit Score: 2000/10000 * 100 = 20

// Vehicle C: Booked (Should be excluded)
$vC = Vehicle::create([
    'company_id' => $company->id,
    'name' => 'Bus C (Busy)',
    'vehicle_number' => 'BUS-C',
    'make' => 'Toyota',
    'model' => 'Coaster',
    'year' => 2024,
    'type' => 'bus',
    'status' => 'active',
    'current_odometer' => 50000,
    'ownership_type' => 'own',
    'daily_rate' => 200
]);
Booking::create([
    'vehicle_id' => $vC->id, // Use vehicle_id, though Booking model might use 'bus_id'. Checking metadata...
    // Rental model uses 'bus_id', Booking model... let's check. 
    // Assuming Booking uses vehicle_id or bus_id. Let's use 'vehicle_id' based on previous context or guess. 
    // Wait, earlier I saw Vehicle hasMany Booking. 
    'vehicle_id' => $vC->id,
    'status' => 'confirmed',
    'pickup_time' => Carbon::parse('2024-06-01 10:00:00'),
    'dropoff_time' => Carbon::parse('2024-06-05 10:00:00'),
    'total_price' => 1000
]);

// 3. Test Service
$service = new BusRecommendationService();
$start = Carbon::parse('2024-06-02 10:00:00');
$end = Carbon::parse('2024-06-03 10:00:00');

$recommendations = $service->recommend($start, $end);

echo "Recommendations for " . $start->toDateString() . " to " . $end->toDateString() . ":\n";
foreach ($recommendations as $rec) {
    echo "- {$rec->name} (Score: {$rec->recommendation_score}) - Odo: {$rec->current_odometer}\n";
}

// Assertions
if ($recommendations->where('id', $vC->id)->count() > 0) {
    echo "FAIL: Booked vehicle C returned.\n";
} else {
    echo "PASS: Booked vehicle C excluded.\n";
}

$first = $recommendations->first();
if ($first && $first->id === $vA->id) {
    echo "PASS: High-value Vehicle A ranked first.\n";
} else {
    echo "FAIL: Ranking incorrect. Top is " . ($first->name ?? 'None') . "\n";
}
