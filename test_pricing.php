<?php

use App\Models\Rental;
use App\Models\PricingRule;
use App\Models\Vehicle;
use App\Models\Company;
use App\Services\RentalPriceCalculator;
use Carbon\Carbon;

// 1. Setup Data
$company = Company::first();
$vehicle = Vehicle::first();

if (!$company || !$vehicle) {
    echo "Error: robust data missing.\n";
    exit;
}

// Ensure a pricing policy exists (mocking or relying on existing)
// For this test, we assume base logic works. We test the RULE application.

// 2. Create a "Weekend Surge" Rule
PricingRule::updateOrCreate(
    ['name' => 'Weekend Surge Test', 'company_id' => $company->id],
    [
        'rule_type' => 'weekend',
        'conditions' => ['days' => ['Sat', 'Sun']],
        'adjustment_type' => 'percentage',
        'adjustment_value' => 10.00, // +10%
        'priority' => 1,
        'is_active' => true
    ]
);

// 3. Create a Rental on a Saturday
$rental = new Rental();
$rental->company_id = $company->id;
$rental->vehicle_id = $vehicle->id;
$rental->rental_type = 'daily';
$rental->status = 'active';
// Force a Saturday
$rental->start_datetime = Carbon::parse('next Saturday 10:00:00');
$rental->end_datetime = Carbon::parse('next Sunday 10:00:00'); // 1 day
$rental->customer_id = 1; // Dummy
$rental->created_by = 1;

// We need to mock the Policy for the calculator to work (it fetches from DB)
// If no policy exists for 'daily', the calculator returns 0.
// Let's assume a policy exists or create a dummy one if needed.
// For now, let's run it.

$calc = new RentalPriceCalculator();
$result = $calc->calculate($rental);

echo "Base Rent: " . $result->base_rent . "\n";
echo "Dynamic Adjustments: " . ($result->line_items['dynamic_adjustments'] ?? 0) . "\n";
echo "Total: " . $result->total . "\n";

if (($result->line_items['dynamic_adjustments'] ?? 0) > 0) {
    echo "SUCCESS: Dynamic pricing applied!\n";
    print_r($result->line_items['applied_rules']);
} else {
    echo "WARNING: No dynamic adjustment. Check if base policy exists or if date is correct.\n";
}
