<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\SubscriptionPlan;
use App\Services\Fleet\FleetOperationsService;

class VehicleLockDuringMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;
    protected $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::firstOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'features' => [], 'is_active' => true]
        );

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'company_admin',
        ]);
        $this->vehicle = Vehicle::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'available',
        ]);
    }

    public function test_vehicle_is_locked_when_maintenance_is_in_progress()
    {
        $this->actingAs($this->user);

        // Create an in_progress maintenance record
        $record = MaintenanceRecord::create([
            'company_id' => $this->company->id,
            'vehicle_id' => $this->vehicle->id,
            'maintenance_number' => 'MNT-000001',
            'type' => 'corrective',
            'status' => 'in_progress',
            'start_date' => now(),
            'created_by' => $this->user->id,
        ]);

        $this->vehicle->refresh();
        $this->assertEquals(Vehicle::STATUS_MAINTENANCE, $this->vehicle->status);

        // Verify FleetOperationsService blocks rental
        $fleetService = app(FleetOperationsService::class);
        $isAvailable = $fleetService->checkVehicleAvailability($this->vehicle, now(), now()->addDays(2));
        $this->assertFalse($isAvailable);

        // Complete the record
        $record->update(['status' => 'completed']);

        $this->vehicle->refresh();
        $this->assertEquals(Vehicle::STATUS_AVAILABLE, $this->vehicle->status);

        // Verify it is available again
        $isAvailableAfter = $fleetService->checkVehicleAvailability($this->vehicle, now(), now()->addDays(2));
        $this->assertTrue($isAvailableAfter);
    }
}
