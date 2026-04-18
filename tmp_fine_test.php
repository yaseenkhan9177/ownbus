<?php

use App\Models\VehicleFine;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Company;
use App\Services\FineService;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first();
$user = $company->users()->where('role', 'admin')->first();
Auth::login($user);

$vehicle = Vehicle::where('company_id', $company->id)->first();
$driver = Driver::where('company_id', $company->id)->first();
$customer = Customer::where('company_id', $company->id)->first();

$fineService = app(FineService::class);

echo "Testing Driver Responsibility...\n";
$fineDriver = $fineService->createFine([
    'company_id' => $company->id,
    'branch_id' => $user->branch_id,
    'vehicle_id' => $vehicle->id,
    'driver_id' => $driver->id,
    'responsible_type' => 'driver',
    'fine_type' => 'Traffic Violation',
    'fine_number' => 'TEST-DRV-1',
    'fine_date' => now(),
    'amount' => 500,
    'status' => 'paid',
    'paid_at' => now(),
    'payment_reference' => 'CASH-1',
    'source' => 'System',
    'authority' => 'Police',
]);

$jeDriver = JournalEntry::where('reference', 'FINE-RECOV-TEST-DRV-1')->first();
echo "Journal Entry found: " . ($jeDriver ? 'Yes' : 'No') . "\n";
if ($jeDriver) {
    echo "Lines: " . count($jeDriver->lines) . "\n";
}

echo "\nTesting Customer Responsibility...\n";
$fineCustomer = $fineService->createFine([
    'company_id' => $company->id,
    'branch_id' => $user->branch_id,
    'vehicle_id' => $vehicle->id,
    'customer_id' => $customer->id,
    'responsible_type' => 'customer',
    'fine_type' => 'Vehicle Damage',
    'fine_number' => 'TEST-CUST-1',
    'fine_date' => now(),
    'amount' => 1500,
    'status' => 'paid',
    'paid_at' => now(),
    'payment_reference' => 'BANK-1',
    'source' => 'System',
    'authority' => 'Police',
]);

$jeCustomer = JournalEntry::where('reference', 'FINE-RECOV-TEST-CUST-1')->first();
echo "Journal Entry found: " . ($jeCustomer ? 'Yes' : 'No') . "\n";
if ($jeCustomer) {
    echo "Lines: " . count($jeCustomer->lines) . "\n";
}

// Cleanup
$fineDriver->forceDelete();
$fineCustomer->forceDelete();
if ($jeDriver) {
    $jeDriver->lines()->delete();
    $jeDriver->delete();
}
if ($jeCustomer) {
    $jeCustomer->lines()->delete();
    $jeCustomer->delete();
}

echo "\nTests completed.\n";
