<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Contract;
use App\Services\Intelligence\ExecutiveDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ContractModuleTest extends TestCase
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

    public function test_contract_can_be_created_and_helpers_work()
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);

        $contract = Contract::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'contract_number' => 'CON-1001',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(20),
            'contract_value' => 5000,
            'monthly_rate' => 1000,
            'status' => 'active',
        ]);

        $this->assertTrue($contract->isActive());
        $this->assertTrue($contract->isExpiringSoon());

        $contract->update(['status' => 'draft']);
        $this->assertFalse($contract->isActive());

        $contract->update(['end_date' => now()->addDays(60)]);
        // \Illuminate\Support\Facades\Log::info('Diff: ' . $contract->end_date->diffInDays(now()));
        // dump($contract->end_date->toDateString(), now()->toDateString(), $contract->end_date->diffInDays(now()));
        $this->assertFalse($contract->isExpiringSoon());
    }

    public function test_contract_relationships()
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);
        $driver = Driver::factory()->create(['company_id' => $company->id]);

        $contract = Contract::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'contract_number' => 'CON-1002',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'contract_value' => 1000,
            'status' => 'active',
        ]);

        $this->assertEquals($customer->id, $contract->customer->id);
        $this->assertEquals($vehicle->id, $contract->vehicle->id);
        $this->assertEquals($driver->id, $contract->driver->id);

        $this->assertTrue($customer->contracts->contains($contract));
        $this->assertTrue($vehicle->contracts->contains($contract));
    }

    public function test_dashboard_kpis_reflect_contract_data()
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $company->id]);

        $v1 = Vehicle::factory()->create(['company_id' => $company->id]);
        $v2 = Vehicle::factory()->create(['company_id' => $company->id]);
        $v3 = Vehicle::factory()->create(['company_id' => $company->id]);

        // Active contract expiring soon
        Contract::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'vehicle_id' => $v1->id,
            'contract_number' => 'CON-ACT-1',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addDays(15),
            'monthly_rate' => 1200,
            'contract_value' => 14400,
            'status' => 'active',
        ]);

        // Active contract NOT expiring soon
        Contract::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'vehicle_id' => $v2->id,
            'contract_number' => 'CON-ACT-2',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addDays(60),
            'monthly_rate' => 800,
            'contract_value' => 9600,
            'status' => 'active',
        ]);

        // Draft contract (should not be counted in KPIs)
        Contract::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'vehicle_id' => $v3->id,
            'contract_number' => 'CON-DFT',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'monthly_rate' => 500,
            'contract_value' => 6000,
            'status' => 'draft',
        ]);

        /** @var ExecutiveDashboardService $service */
        $service = app(ExecutiveDashboardService::class);
        $stats = $service->getDashboardStats($company->id);

        $this->assertArrayHasKey('contracts', $stats);
        $this->assertEquals(2, $stats['contracts']['active_contracts']);
        $this->assertEquals(1, $stats['contracts']['expiring_soon']);
        $this->assertEquals(2000, $stats['contracts']['monthly_revenue']); // 1200 + 800
        $this->assertEquals(66.67, $stats['contracts']['allocation_rate']); // 2/3 vehicles
    }
}
