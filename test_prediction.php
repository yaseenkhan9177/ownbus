<?php

use App\Models\Vehicle;
use App\Models\BusProfitabilityMetric;
use App\Services\PredictiveMaintenanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// 1. Setup Data
$vehicle = Vehicle::first();
if (!$vehicle) {
    echo "No vehicles found.\n";
    exit;
}

// Simulate high usage
$vehicle->current_odometer = 9500; // Close to 10k service
$vehicle->last_service_odometer = 0;
$vehicle->save();

// Simulate metrics for daily avg calculation
BusProfitabilityMetric::create([
    'company_id' => $vehicle->company_id,
    'vehicle_id' => $vehicle->id,
    'month_year' => Carbon::now()->format('Y-m'),
    'total_km' => 3000, // 100km/day
    'total_revenue' => 1000,
    'total_expense' => 0,
    'net_profit' => 1000,
    'trips_completed' => 10,
    'maintenance_cost' => 0,
    'days_rented' => 30,
]);

// 2. Run Service
$service = new PredictiveMaintenanceService();
$service->generatePredictions();

// 3. Check Results
$prediction = \App\Models\MaintenancePrediction::where('vehicle_id', $vehicle->id)
    ->where('prediction_type', 'mileage')
    ->first();

if ($prediction) {
    echo "SUCCESS: Prediction created for Vehicle {$vehicle->id}.\n";
    echo "Reason: {$prediction->reason}\n";
    echo "Predicted Date: {$prediction->predicted_date->format('Y-m-d')}\n";
} else {
    echo "FAILED: No prediction created.\n";
}
