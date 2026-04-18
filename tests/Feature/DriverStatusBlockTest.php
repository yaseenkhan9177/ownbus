<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

/**
 * @property \App\Models\Company $company
 * @property \App\Models\User $user
 * @property \App\Models\Vehicle $vehicle
 * @property \App\Models\Customer $customer
 */
class DriverStatusBlockTest extends TestCase
{
    use RefreshDatabase;

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
        $this->vehicle = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => Vehicle::STATUS_AVAILABLE]);
        $this->customer = Customer::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_cannot_assign_suspended_driver_to_rental()
    {
        $driver = Driver::factory()->create([
            'company_id' => $this->company->id,
            'status' => Driver::STATUS_SUSPENDED,
        ]);

        $rentalData = [
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $driver->id,
            'rental_type' => 'daily',
            'rate_type' => 'daily',
            'rate_amount' => 100,
            'start_date' => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'end_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d H:i:s'),
            'pickup_location' => 'HQ',
            'dropoff_location' => 'HQ',
        ];

        $response = $this->actingAs($this->user)->post(route('company.rentals.store'), $rentalData);

        $response->assertSessionHasErrors(['driver_id']);

        $this->assertDatabaseMissing('rentals', [
            'driver_id' => $driver->id,
        ]);
    }
}
