<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FleetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed subscription plan
        \App\Models\SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'stripe_product_id' => 'prod_test',
            'stripe_price_id' => 'price_test',
            'price_monthly' => 10,
            'price_yearly' => 100,
            'trial_days' => 14,
            'is_active' => true,
            'features' => json_encode(['fleet_management', 'basic_reporting']), // Add dummy features
        ]);

        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'company_admin',
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function can_view_fleet_list()
    {
        $vehicle = Vehicle::factory()->create(['company_id' => $this->company->id]);

        $response = $this->get(route('company.fleet.index'));

        $response->assertStatus(200);
        $response->assertSee($vehicle->vehicle_number);
    }

    /** @test */
    public function cannot_view_other_company_vehicles()
    {
        $otherCompany = Company::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['company_id' => $otherCompany->id, 'vehicle_number' => 'OTHER-123']);

        $response = $this->get(route('company.fleet.index'));

        $response->assertStatus(200);
        $response->assertDontSee('OTHER-123');
    }

    /** @test */
    public function can_create_vehicle()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('bus.jpg', 100);

        $data = [
            'vehicle_number' => 'BUS-999',
            'name' => 'Test Bus',
            'type' => 'bus',
            'seating_capacity' => 50,
            'fuel_type' => 'diesel',
            'transmission' => 'manual',
            'model_year' => 2024,
            'make' => 'Toyota',
            'model' => 'Coaster',
            'year' => 2024,
            'color' => 'White',
            'current_odometer' => 1000,
            'next_service_odometer' => 10000,
            'status' => 'available',
            'daily_rate' => 500,
            'image' => $file,
        ];

        $response = $this->post(route('company.fleet.store'), $data);

        $response->assertRedirect(route('company.fleet.index'));
        $this->assertDatabaseHas('vehicles', [
            'vehicle_number' => 'BUS-999',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function validation_errors_on_create()
    {
        $response = $this->post(route('company.fleet.store'), []);

        $response->assertSessionHasErrors(['vehicle_number', 'name', 'type']);
    }

    /** @test */
    public function can_update_vehicle()
    {
        $vehicle = Vehicle::factory()->create(['company_id' => $this->company->id]);

        $response = $this->put(route('company.fleet.update', $vehicle), [
            'vehicle_number' => $vehicle->vehicle_number,
            'name' => 'Updated Name',
            'type' => 'bus', // key: match validation
            'seating_capacity' => 45,
            'fuel_type' => 'diesel',
            'transmission' => 'automatic',
            'model_year' => 2023,
            'make' => 'Toyota',
            'model' => 'Coaster',
            'year' => 2023,
            'color' => 'Black',
            'current_odometer' => 12000,
            'next_service_odometer' => 15000,
            'status' => 'maintenance',
            'daily_rate' => 600,
        ]);

        $response->assertRedirect(route('company.fleet.index'));
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'name' => 'Updated Name',
            'status' => 'maintenance',
        ]);
    }

    /** @test */
    public function cannot_update_other_company_vehicle()
    {
        $otherCompany = Company::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->put(route('company.fleet.update', $otherVehicle), [
            'vehicle_number' => 'HACK-123',
            'name' => 'Hacked Name',
            'type' => 'bus',
            'seating_capacity' => 50,
            'fuel_type' => 'diesel',
            'transmission' => 'manual',
            'model_year' => 2024,
            'make' => 'Nissan',
            'model' => 'Urvan',
            'year' => 2024,
            'color' => 'Silver',
            'current_odometer' => 5000,
            'next_service_odometer' => 10000,
            'status' => 'available',
            'daily_rate' => 100,
        ]);

        $response->assertStatus(404); // Authorization/Scoping failure (Not Found)
    }
}
