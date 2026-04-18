<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\BusProfitabilityMetric;
use App\Models\BusUtilizationLog;
use App\Models\SubscriptionPlan;
use App\Services\BusRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BusRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $company;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::firstOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'features' => [], 'is_active' => true]
        );

        $this->service = app(BusRecommendationService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    #[Test]
    public function it_filters_out_booked_vehicles()
    {
        $v1 = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available']);
        $v2 = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available']);

        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
        ]);

        // Book v1 for overlapping time
        Rental::factory()->create([
            'company_id' => $this->company->id,
            'vehicle_id' => $v1->id,
            'status' => 'assigned',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
        ]);

        $recommendations = $this->service->recommendBuses($rental);

        $this->assertCount(1, $recommendations);
        $this->assertEquals($v2->id, $recommendations->first()['vehicle']->id);
    }

    #[Test]
    public function it_ranks_by_profitability()
    {
        $v1 = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available']);
        $v2 = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available']);

        // v1 is high profit
        BusProfitabilityMetric::create([
            'vehicle_id' => $v1->id,
            'month_year' => now()->format('Y-m'),
            'net_profit' => 3000,
        ]);
        // v2 is low profit
        BusProfitabilityMetric::create([
            'vehicle_id' => $v2->id,
            'month_year' => now()->format('Y-m'),
            'net_profit' => 500,
        ]);

        $rental = Rental::factory()->create(['company_id' => $this->company->id]);
        $recommendations = $this->service->recommendBuses($rental);

        $this->assertEquals($v1->id, $recommendations->first()['vehicle']->id);
        $this->assertEquals('Top earner: Consistent high-margin performance.', $recommendations->first()['reason']);
    }

    #[Test]
    public function it_ranks_by_utilization_rotation()
    {
        $v1 = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available']);
        $v2 = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available']);

        // v1 was heavily used (100 hours)
        BusUtilizationLog::create([
            'company_id' => $this->company->id,
            'bus_id' => $v1->id,
            'hours_used' => 100,
            'date' => now(),
        ]);
        // v2 was idle
        BusUtilizationLog::create([
            'company_id' => $this->company->id,
            'bus_id' => $v2->id,
            'hours_used' => 0,
            'date' => now(),
        ]);

        $rental = Rental::factory()->create(['company_id' => $this->company->id]);
        $recommendations = $this->service->recommendBuses($rental);

        // v2 should be higher score because it's idle (wear balancing)
        $this->assertEquals($v2->id, $recommendations->first()['vehicle']->id);
        $this->assertTrue($recommendations->first()['breakdown']['utilization'] > $recommendations->last()['breakdown']['utilization']);
    }

    #[Test]
    public function it_respects_maintenance_buffer()
    {
        // v1 has 9,900km (close to 10k threshold)
        $v1 = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available', 'current_odometer' => 9900]);
        // v2 has 1,000km (large buffer)
        $v2 = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available', 'current_odometer' => 1000]);

        $rental = Rental::factory()->create(['company_id' => $this->company->id]);
        $recommendations = $this->service->recommendBuses($rental);

        $this->assertTrue($recommendations->where('vehicle.id', $v2->id)->first()['breakdown']['maintenance'] >
            $recommendations->where('vehicle.id', $v1->id)->first()['breakdown']['maintenance']);
    }

    #[Test]
    public function api_endpoint_returns_json_recommendations()
    {
        $vehicle = Vehicle::factory()->create(['company_id' => $this->company->id, 'status' => 'available']);
        $rental = Rental::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/rentals/{$rental->id}/recommendations");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'rental_uuid',
                'recommendations' => [
                    '*' => ['vehicle', 'score', 'breakdown', 'recommendation_reason']
                ]
            ]);
    }
}
