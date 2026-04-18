<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

/**
 * @property \App\Models\Company $company
 * @property \App\Models\User $user
 * @property \App\Models\Customer $customer
 */
class DriverDoubleAssignmentTest extends TestCase
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
        $this->customer = Customer::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_cannot_assign_driver_to_overlapping_rental()
    {
        $driver = Driver::factory()->create([
            'company_id' => $this->company->id,
            'status' => Driver::STATUS_ACTIVE,
        ]);

        Rental::factory()->create([
            'company_id' => $this->company->id,
            'driver_id' => $driver->id,
            'status' => Rental::STATUS_ACTIVE,
            'start_date' => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'end_date' => Carbon::tomorrow()->addDays(5)->format('Y-m-d H:i:s'),
            'customer_id' => $this->customer->id,
            'vehicle_id' => Vehicle::factory()->create(['company_id' => $this->company->id])->id,
        ]);

        $vehicle = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => Vehicle::STATUS_AVAILABLE]);

        $rentalData = [
            'customer_id' => $this->customer->id,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'rental_type' => 'daily',
            'rate_type' => 'daily',
            'rate_amount' => 100,
            'start_date' => Carbon::tomorrow()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d H:i:s'),
            'pickup_location' => 'HQ',
            'dropoff_location' => 'HQ',
        ];

        $response = $this->actingAs($this->user)->post(route('company.rentals.store'), $rentalData);

        $response->assertSessionHasErrors(['driver_id']);
    }
}
