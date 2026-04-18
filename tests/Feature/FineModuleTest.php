<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleFine;
use App\Services\Intelligence\ExecutiveDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FineModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default subscription plan required by CompanyObserver
        \App\Models\SubscriptionPlan::create([
            'name' => 'Starter Plan',
            'slug' => 'starter',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'is_active' => true,
            'trial_days' => 14,
            'version' => 1,
            'features' => [],
        ]);
    }

    public function test_vehicle_fine_can_be_created_and_helpers_work()
    {
        $company = Company::factory()->create();
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);

        $fine = VehicleFine::create([
            'company_id' => $company->id,
            'vehicle_id' => $vehicle->id,
            'authority' => 'Dubai Police',
            'fine_number' => 'FN-12345',
            'fine_date' => now()->subDays(5),
            'due_date' => now()->subDay(),
            'amount' => 500.00,
            'status' => 'pending',
            'customer_responsible' => true,
        ]);

        $this->assertDatabaseHas('vehicle_fines', [
            'fine_number' => 'FN-12345',
            'amount' => 500.00
        ]);

        $this->assertTrue($fine->isOverdue());

        $fine->update(['status' => 'paid']);
        $this->assertFalse($fine->isOverdue());

        $fine->update(['status' => 'pending', 'due_date' => now()->addDays(10)]);
        $this->assertFalse($fine->isOverdue());
    }

    public function test_fine_relationships()
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);
        $driver = Driver::factory()->create(['company_id' => $company->id]);

        $fine = VehicleFine::create([
            'company_id' => $company->id,
            'vehicle_id' => $vehicle->id,
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'authority' => 'Abu Dhabi Police',
            'fine_date' => now(),
            'amount' => 200,
            'status' => 'pending',
        ]);

        $this->assertEquals($vehicle->id, $fine->vehicle->id);
        $this->assertEquals($customer->id, $fine->customer->id);
        $this->assertEquals($driver->id, $fine->driver->id);

        $this->assertTrue($vehicle->fines->contains($fine));
    }

    public function test_dashboard_fine_kpis()
    {
        $company = Company::factory()->create();
        $v1 = Vehicle::factory()->create(['company_id' => $company->id, 'name' => 'Bus A', 'vehicle_number' => 'A1']);
        $v2 = Vehicle::factory()->create(['company_id' => $company->id, 'name' => 'Bus B', 'vehicle_number' => 'B2']);

        // Pending fine (overdue)
        VehicleFine::create([
            'company_id' => $company->id,
            'vehicle_id' => $v1->id,
            'authority' => 'Authority X',
            'fine_date' => now()->subDays(40),
            'due_date' => now()->subDays(10),
            'amount' => 1000,
            'status' => 'pending',
        ]);

        // Pending fine (not overdue)
        VehicleFine::create([
            'company_id' => $company->id,
            'vehicle_id' => $v2->id,
            'authority' => 'Authority Y',
            'fine_date' => now()->subDays(5),
            'due_date' => now()->addDays(5),
            'amount' => 500,
            'status' => 'pending',
        ]);

        // Paid fine (should be excluded from pending/overdue)
        VehicleFine::create([
            'company_id' => $company->id,
            'vehicle_id' => $v1->id,
            'authority' => 'Authority X',
            'fine_date' => now()->subDays(60),
            'due_date' => now()->subDays(30),
            'amount' => 300,
            'status' => 'paid',
        ]);

        /** @var ExecutiveDashboardService $service */
        $service = app(ExecutiveDashboardService::class);
        $stats = $service->getDashboardStats($company->id);

        $this->assertArrayHasKey('traffic_compliance', $stats);
        $this->assertEquals(2, $stats['traffic_compliance']['pending_count']);
        $this->assertEquals(1500, $stats['traffic_compliance']['total_amount']);
        $this->assertEquals(1, $stats['traffic_compliance']['overdue_count']);
        $this->assertEquals(2, $stats['traffic_compliance']['vehicles_at_risk']);

        $this->assertCount(2, $stats['traffic_compliance']['top_vehicles']);
        $this->assertEquals('A1 - Bus A', $stats['traffic_compliance']['top_vehicles'][0]['vehicle']);
        $this->assertEquals(1000, $stats['traffic_compliance']['top_vehicles'][0]['amount']);
    }
}
