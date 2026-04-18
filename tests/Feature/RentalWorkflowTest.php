<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Rental;
use App\Services\RentalPriceCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

class RentalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;
    protected $vehicle;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Company & User
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'company_admin' // Valid enum value
        ]);

        // 2. Setup Vehicle
        $this->vehicle = Vehicle::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
            'daily_rate' => 1000,
        ]);

        // 3. Setup Customer
        $this->customer = Customer::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    #[Test]
    public function can_create_a_rental_booking()
    {
        $this->actingAs($this->user);

        // 3. Create Rental Request
        $validData = [
            'customer_id' => $this->customer->id,
            'rental_type' => 'daily',
            'start_datetime' => now()->addDay()->toDateTimeString(),
            'end_datetime' => now()->addDays(4)->toDateTimeString(),
            'pickup_location' => 'Dubai Airport',
            'dropoff_location' => 'Hotel XYZ',
            // 'bus_id' => $this->vehicle->id, // Controller might not accept this on creation
        ];

        $response = $this->post(route('rentals.store'), $validData);

        $response->assertRedirect();

        // Follow redirect to see if it errored there? Or just check DB.
        $this->assertDatabaseHas('rentals', [
            'customer_id' => $this->customer->id,
            'pickup_location' => 'Dubai Airport',
            'status' => 'draft', // Controller sets initial status to draft
        ]);
    }

    #[Test]
    public function can_dispatch_an_assigned_rental()
    {
        // Create a rental in 'assigned' status, with a bus assigned
        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'bus_id' => $this->vehicle->id,
            'status' => 'assigned',
            'start_datetime' => now()->addDay(),
        ]);

        $this->actingAs($this->user);

        // Transition to 'dispatched' (valid transition from assigned)
        $response = $this->post(route('rentals.transition', $rental), [
            'to_status' => 'dispatched',
            'reason' => 'Customer picked up vehicle',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'status' => 'dispatched',
        ]);
    }
}
