<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractInvoice;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleFine;
use App\Services\RiskCenterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiskCenterServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RiskCenterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RiskCenterService();

        // Seed default subscription plan required by CompanyObserver if necessary
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

    public function test_aggregates_critical_risks_correctly()
    {
        $company = Company::factory()->create();
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);

        // 1. Expired Driver License
        Driver::factory()->create([
            'company_id' => $company->id,
            'status' => 'active',
            'license_expiry_date' => now()->subDays(5),
            'first_name' => 'Expired',
            'last_name' => 'Driver'
        ]);

        // 2. Expired Vehicle Mulkiya
        Vehicle::factory()->create([
            'company_id' => $company->id,
            'status' => 'available',
            'registration_expiry' => now()->subDays(2),
            'vehicle_number' => 'EXP-001'
        ]);

        // 3. Overdue Fine > 30 Days
        VehicleFine::create([
            'company_id' => $company->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'pending',
            'due_date' => now()->subDays(35),
            'fine_date' => now()->subDays(40),
            'fine_number' => 'FINE-123',
            'amount' => 500,
            'source' => 'RTA'
        ]);

        $summary = $this->service->getCompanyRiskSummary($company);

        $this->assertCount(3, $summary['critical']);

        $types = collect($summary['critical'])->pluck('type');
        $this->assertTrue($types->contains('driver_license_expired'));
        $this->assertTrue($types->contains('vehicle_mulkiya_expired'));
        $this->assertTrue($types->contains('fine_overdue_30'));
    }

    public function test_aggregates_warning_risks_correctly()
    {
        $company = Company::factory()->create();

        // 1. License expiring in 3 days
        Driver::factory()->create([
            'company_id' => $company->id,
            'status' => 'active',
            'license_expiry_date' => now()->addDays(3),
            'first_name' => 'Expiring',
            'last_name' => 'Soon'
        ]);

        // 2. Maintenance Overdue
        Vehicle::factory()->create([
            'company_id' => $company->id,
            'status' => 'available',
            'current_odometer' => 10500,
            'next_service_odometer' => 10000,
            'vehicle_number' => 'MAINT-001'
        ]);

        $summary = $this->service->getCompanyRiskSummary($company);

        $this->assertCount(2, $summary['warning']);

        $types = collect($summary['warning'])->pluck('type');
        $this->assertTrue($types->contains('driver_license_expiring_soon'));
        $this->assertTrue($types->contains('maintenance_overdue'));
    }

    public function test_aggregates_info_risks_correctly()
    {
        $company = Company::factory()->create();

        // 1. Contract expiring in 10 days
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);
        Contract::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'contract_number' => 'CON-123',
            'start_date' => now()->subMonths(11),
            'end_date' => now()->addDays(10),
            'status' => 'active',
            'contract_value' => 1000
        ]);

        $summary = $this->service->getCompanyRiskSummary($company);

        $this->assertCount(1, $summary['info']);
        $this->assertEquals('contract_expiring_soon', $summary['info'][0]['type']);
    }
}
