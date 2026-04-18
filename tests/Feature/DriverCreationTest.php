<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @property \App\Models\Company $company
 * @property \App\Models\User $user
 */
class DriverCreationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'version' => '1.0',
            'price_monthly' => 39.00,
            'price_yearly' => 0.00,
            'features' => [],
            'is_active' => true,
        ]);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id, 'role' => 'company_admin']);
    }

    public function test_admin_can_view_driver_creation_page()
    {
        $response = $this->actingAs($this->user)->get(route('company.drivers.create'));
        $response->assertStatus(200);
        $response->assertViewIs('portal.drivers.create');
    }

    public function test_admin_can_create_driver_with_valid_data()
    {
        $driverData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'national_id' => $this->faker->unique()->numerify('#####-#######-#'),
            'license_number' => $this->faker->unique()->bothify('??-####-####'),
            'license_expiry_date' => now()->addYears(2)->format('Y-m-d'),
            'license_type' => 'heavy',
            'hire_date' => now()->subDays(10)->format('Y-m-d'),
            'salary' => 5000,
            'commission_rate' => 5,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'emergency_contact_name' => $this->faker->name,
            'emergency_contact_phone' => $this->faker->phoneNumber,
        ];

        $response = $this->actingAs($this->user)->post(route('company.drivers.store'), $driverData);

        $driver = Driver::first();

        $response->assertRedirect(route('company.drivers.show', $driver));

        $this->assertDatabaseHas('drivers', [
            'company_id' => $this->company->id,
            'email' => $driverData['email'],
            'license_number' => $driverData['license_number'],
            'status' => Driver::STATUS_ACTIVE,
        ]);

        // Assert driver code is generated
        $this->assertNotNull($driver->driver_code);
        $this->assertStringStartsWith('DRV-', $driver->driver_code);
    }
}
