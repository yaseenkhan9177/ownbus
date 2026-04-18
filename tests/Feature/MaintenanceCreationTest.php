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

class MaintenanceCreationTest extends TestCase
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

    public function test_can_create_maintenance_record_with_items_and_calculate_total()
    {
        $this->actingAs($this->user);

        $payload = [
            'vehicle_id' => $this->vehicle->id,
            'type' => 'preventive',
            'status' => 'in_progress',
            'items' => [
                [
                    'item_type' => 'part',
                    'description' => 'Oil Filter',
                    'quantity' => 2,
                    'unit_cost' => 50.00
                ],
                [
                    'item_type' => 'labor',
                    'description' => 'Mechanic Time',
                    'quantity' => 1.5,
                    'unit_cost' => 100.00
                ]
            ]
        ];

        $response = $this->post(route('company.maintenance.store'), $payload);

        $response->assertRedirect(route('company.maintenance.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('maintenance_records', [
            'vehicle_id' => $this->vehicle->id,
            'type' => 'preventive',
            'status' => 'in_progress',
            'total_cost' => 250.00, // (2*50) + (1.5*100) = 100 + 150 = 250
        ]);

        $record = MaintenanceRecord::where('vehicle_id', $this->vehicle->id)->first();

        $this->assertDatabaseHas('maintenance_items', [
            'maintenance_record_id' => $record->id,
            'item_type' => 'part',
            'total_cost' => 100.00,
        ]);

        $this->assertDatabaseHas('maintenance_items', [
            'maintenance_record_id' => $record->id,
            'item_type' => 'labor',
            'total_cost' => 150.00,
        ]);
    }
}
