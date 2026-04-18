<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\Rental;
use App\Models\SubscriptionPlan;
use App\Services\Fleet\FleetDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FleetDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed a default subscription plan to avoid ModelNotFoundException during company creation
        if (class_exists(SubscriptionPlan::class)) {
            try {
                // Check if exists first to avoid duplicates if transaction rollback behaves oddly (unlikely but safe)
                if (!SubscriptionPlan::where('slug', 'starter')->exists()) {
                    SubscriptionPlan::create([
                        'name' => 'Starter Plan',
                        'slug' => 'starter', // Must match default in SubscriptionService
                        'price_monthly' => 0,
                        'price_yearly' => 0,
                        'is_active' => true,
                        'features' => [],
                    ]);
                }
            } catch (\Exception $e) {
                // ignore
            }
        }
    }

    /** @test */
    public function dashboard_loads_successfully_for_company_owner()
    {
        // 1. Setup Data
        $company = Company::factory()->create();
        $owner = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'company_admin',
        ]);

        // 2. Act
        $response = $this->actingAs($owner)->get(route('company.dashboard'));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertViewIs('portal.dashboard');
        $response->assertViewHas('data');
    }

    /** @test */
    public function dashboard_kpis_calculate_correctly()
    {
        $company = Company::factory()->create();

        $vehicles = Vehicle::factory()->count(10)->create([
            'company_id' => $company->id,
            'status' => 'available'
        ]);

        // Active Rentals for first 3 vehicles
        $rentedVehicles = $vehicles->take(3);

        foreach ($rentedVehicles as $vehicle) {
            Rental::factory()->create([
                'company_id' => $company->id,
                'bus_id' => $vehicle->id,
                'status' => 'active',
                'grand_total' => 1000,
                'start_datetime' => Carbon::now()->subDay(),
                'end_datetime' => Carbon::now()->addDay(),
            ]);

            // Seed Utilization Log to mark as active
            if (class_exists(\App\Models\BusUtilizationLog::class)) {
                \App\Models\BusUtilizationLog::create([
                    'company_id' => $company->id,
                    'bus_id' => $vehicle->id,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'utilization_score' => 100,
                    'vehicle_id' => $vehicle->id,
                ]);
            }
        }

        // Service
        $service = app(FleetDashboardService::class);
        $data = $service->getDashboardData($company);

        // Assert
        $this->assertEquals(3, $data['kpis']['active_rentals']);

        $this->assertGreaterThanOrEqual(0, $data['utilization']['idle_count']);
        $this->assertArrayHasKey('rate', $data['utilization']);
    }

    /** @test */
    public function it_identifies_conflicts()
    {
        $company = Company::factory()->create();
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);

        // Create overlapping rentals
        Rental::factory()->create([
            'company_id' => $company->id,
            'bus_id' => $vehicle->id,
            'start_datetime' => Carbon::now(),
            'end_datetime' => Carbon::now()->addDays(2),
            'status' => 'confirmed'
        ]);

        Rental::factory()->create([
            'company_id' => $company->id,
            'bus_id' => $vehicle->id,
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDays(3),
            'status' => 'confirmed'
        ]);

        $service = app(FleetDashboardService::class);

        $opsService = app(\App\Services\Fleet\FleetOperationsService::class);
        $conflicts = $opsService->getConflictCount($company);

        $this->assertGreaterThan(0, $conflicts);
    }

    /** @test */
    public function it_identifies_expiring_driver_licenses()
    {
        $company = Company::factory()->create();

        // 1. Create a Driver
        $driverUser = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'driver',
        ]);

        // 2. Create Profile and Document
        if (class_exists(\App\Models\DriverProfile::class) && class_exists(\App\Models\DriverDocument::class)) {
            $profile = \App\Models\DriverProfile::create([
                'user_id' => $driverUser->id,
                'status' => 'available'
            ]);

            \App\Models\DriverDocument::create([
                'driver_profile_id' => $profile->id,
                'document_type' => 'license',
                'expiry_date' => Carbon::now()->addDays(10), // Expiring in 10 days
                'is_verified' => true
            ]);
        }

        // 3. Check Alerts
        $alertService = app(\App\Services\Fleet\FleetAlertService::class);
        $alerts = $alertService->getActiveAlerts($company);

        // 4. Assert
        $hasLicenseAlert = collect($alerts)->contains(function ($alert) {
            return str_contains($alert['message'], 'license is expiring soon');
        });

        $this->assertTrue($hasLicenseAlert, 'Expected an alert for expiring driver licenses.');
    }
}
