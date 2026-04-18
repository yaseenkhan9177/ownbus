<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\BusUtilizationLog;
use App\Models\MaintenancePrediction;
use App\Models\VehicleUnavailability;
use App\Services\MaintenanceService;
use App\Services\AvailabilityService;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PredictiveMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    protected $maintenanceService;
    protected $availabilityService;
    protected $company;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::firstOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'features' => [], 'is_active' => true]
        );

        $this->maintenanceService = app(MaintenanceService::class);
        $this->availabilityService = app(AvailabilityService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    #[Test]
    public function it_generates_prediction_based_on_high_utilization()
    {
        // 1. Setup vehicle at 9,500km
        $vehicle = Vehicle::factory()->create([
            'company_id' => $this->company->id,
            'current_odometer' => 9500,
            'status' => 'available'
        ]);

        // 2. Mock high usage (250km/day)
        BusUtilizationLog::create([
            'company_id' => $this->company->id,
            'bus_id' => $vehicle->id,
            'km_used' => 250,
            'date' => Carbon::now()->subDay(),
        ]);

        // 3. Run prediction
        $this->maintenanceService->predictServiceNeeds($this->company->id);

        // 4. Verify prediction
        $prediction = MaintenancePrediction::where('vehicle_id', $vehicle->id)->first();
        $this->assertNotNull($prediction);
        $this->assertEquals('mileage', $prediction->prediction_type);

        // 500km remaining / 250km per day = 2 days
        $this->assertEquals(Carbon::now()->addDays(2)->toDateString(), $prediction->predicted_date->toDateString());
    }

    #[Test]
    public function api_can_list_and_schedule_predictions()
    {
        $vehicle = Vehicle::factory()->create(['company_id' => $this->company->id]);
        $prediction = MaintenancePrediction::create([
            'company_id' => $this->company->id,
            'vehicle_id' => $vehicle->id,
            'prediction_type' => 'mileage',
            'predicted_date' => Carbon::now()->addDays(5),
            'status' => 'pending'
        ]);

        // List predictions
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/maintenance/predictions');

        $response->assertStatus(200)
            ->assertJsonCount(1);

        // Schedule prediction
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/maintenance/predictions/{$prediction->id}/schedule");

        $response->assertStatus(200);
        $this->assertEquals('scheduled', $prediction->fresh()->status);

        // Verify Unavailability block
        $this->assertTrue(VehicleUnavailability::where('vehicle_id', $vehicle->id)->exists());
    }

    #[Test]
    public function availability_service_blocks_scheduled_predictions()
    {
        $vehicle = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available']);
        $predictionDate = Carbon::now()->addDays(3);

        MaintenancePrediction::create([
            'company_id' => $this->company->id,
            'vehicle_id' => $vehicle->id,
            'prediction_type' => 'mileage',
            'predicted_date' => $predictionDate->toDateString(),
            'status' => 'scheduled'
        ]);

        // Verify record exists before check
        $this->assertEquals(1, MaintenancePrediction::where('vehicle_id', $vehicle->id)->where('status', 'scheduled')->count());

        // Check availability for that date
        $isAvailable = $this->availabilityService->isBusAvailable(
            $vehicle->id,
            $predictionDate->copy()->startOfDay(),
            $predictionDate->copy()->endOfDay()
        );

        $this->assertFalse($isAvailable, "Service should be blocked on scheduled maintenance date.");
    }
}
