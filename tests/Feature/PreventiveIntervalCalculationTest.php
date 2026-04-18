<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\VehicleServiceInterval;
use App\Models\SubscriptionPlan;
use App\Services\Fleet\MaintenanceService;

class PreventiveIntervalCalculationTest extends TestCase
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
            'current_odometer' => 50000,
            'status' => 'available',
        ]);
    }

    public function test_intervals_are_updated_upon_maintenance_completion()
    {
        // Arrange
        $interval = VehicleServiceInterval::create([
            'company_id' => $this->company->id,
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'oil_change',
            'interval_km' => 10000,
            'interval_days' => 180,
            'last_service_odometer' => 40000,
            'last_service_date' => now()->subDays(180),
            'next_due_odometer' => 50000,
            'next_due_date' => now(),
        ]);

        $record = MaintenanceRecord::create([
            'company_id' => $this->company->id,
            'vehicle_id' => $this->vehicle->id,
            'maintenance_number' => 'MNT-000002',
            'type' => 'preventive',
            'status' => 'in_progress',
            'odometer_reading' => 50000,
            'created_by' => $this->user->id,
        ]);

        // Act
        $service = app(MaintenanceService::class);
        $completionDate = now();
        $service->completeRecord($record, [
            'odometer_reading' => 50000,
            'completed_date' => $completionDate,
        ]);

        // Assert
        $interval->refresh();
        $this->assertEquals(50000, $interval->last_service_odometer);
        $this->assertEquals($completionDate->format('Y-m-d'), $interval->last_service_date->format('Y-m-d'));

        // Next due should be +10000 km
        $this->assertEquals(60000, $interval->next_due_odometer);

        // Next due should be +180 days
        $expectedNextDate = $completionDate->copy()->addDays(180)->format('Y-m-d');
        $this->assertEquals($expectedNextDate, $interval->next_due_date->format('Y-m-d'));
    }
}
