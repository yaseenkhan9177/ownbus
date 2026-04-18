<?php

use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Branch;
use App\Services\Intelligence\PricingEngineService;
use Carbon\Carbon;

$pricingEngine = app(PricingEngineService::class);

$vehicle = Vehicle::first();
$customer = Customer::first();
$branch = $vehicle->branch ?: Branch::first();

if (!$branch) {
    echo "Creating dummy branch for test...\n";
    $branch = Branch::create(['name' => 'Dubai Test Branch', 'company_id' => $vehicle->company_id]);
}

$startDate = Carbon::now();

echo "--- PRICING ENGINE VERIFICATION ---\n";
echo "Vehicle: {$vehicle->name} (Base Rate: {$vehicle->daily_rate})\n";
echo "Customer: {$customer->name} (Risk: {$customer->risk_level})\n";
echo "Start Date: {$startDate->toDateString()}\n\n";

$result = $pricingEngine->calculateRate($vehicle, $branch, $customer, $startDate);

echo "Optimized Rate: {$result['optimized_rate']}\n";
echo "Multipliers:\n";
foreach ($result['breakdown'] as $key => $val) {
    echo "  - $key: $val\n";
}

echo "\n--- TESTING SEASONALITY ---\n";
// Create a seasonal rule for today
$rule = \App\Models\SeasonalPricingRule::create([
    'name' => 'High Season Test',
    'start_date' => now()->subDay(),
    'end_date' => now()->addDay(),
    'multiplier' => 1.5,
    'branch_id' => $branch->id,
    'is_active' => true
]);

$resultSeason = $pricingEngine->calculateRate($vehicle, $branch, $customer, $startDate);
echo "Optimized Rate (with 1.5x Season): {$resultSeason['optimized_rate']}\n";
echo "Season Multiplier: {$resultSeason['breakdown']['season_multiplier']}\n";

$rule->delete();

echo "\nVERIFICATION COMPLETE\n";
