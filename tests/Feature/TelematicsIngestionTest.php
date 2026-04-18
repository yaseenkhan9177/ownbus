<?php

namespace Tests\Feature;

use App\Events\Telematics\VehicleLocationUpdated;
use App\Jobs\Telematics\ProcessGpsPing;
use App\Models\Company;
use App\Models\GpsDevice;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class TelematicsIngestionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'SubscriptionPlanSeeder']);

        // Use array driver for Cache to prevent test interference
        config(['cache.default' => 'array']);

        // Mock Redis so we don't pollute local Redis server during testing
        Redis::shouldReceive('set')->andReturn(true);
        Redis::shouldReceive('lpush')->andReturn(true);
        Redis::shouldReceive('get')->andReturn(null);
    }

    public function test_webhook_ingests_valid_payload_and_queues_job()
    {
        Queue::fake();

        $company = Company::factory()->create();
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);
        $device = GpsDevice::factory()->create([
            'company_id' => $company->id,
            'vehicle_id' => $vehicle->id,
            'imei_number' => '123456789012345',
            'status' => 'active'
        ]);

        $payload = [
            'imei' => '123456789012345',
            'lat' => 25.2048,
            'lng' => 55.2708,
            'speed' => 60.5,
            'engine_status' => true,
            'timestamp' => time(),
        ];

        $response = $this->postJson('/api/v1/telematics/ping', $payload);

        $response->assertStatus(202);

        Queue::assertPushed(ProcessGpsPing::class, function ($job) use ($company, $vehicle) {
            return $job->payload['company_id'] === $company->id &&
                $job->payload['vehicle_id'] === $vehicle->id &&
                $job->payload['speed'] === 60.5;
        });
    }

    public function test_webhook_rejects_invalid_imei()
    {
        Queue::fake();

        $payload = [
            'imei' => 'UNKNOWN_IMEI',
            'lat' => 25.2048,
            'lng' => 55.2708,
            'speed' => 0,
            'engine_status' => false,
            'timestamp' => time(),
        ];

        $response = $this->postJson('/api/v1/telematics/ping', $payload);

        $response->assertStatus(404)
            ->assertJsonPath('error', 'Device not registered or inactive');

        Queue::assertNothingPushed();
    }

    public function test_job_inserts_to_database_and_broadcasts_event()
    {
        Event::fake([VehicleLocationUpdated::class]);

        $company = Company::factory()->create();
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);

        $payload = [
            'company_id' => $company->id,
            'vehicle_id' => $vehicle->id,
            'imei' => '12345',
            'latitude' => 25.0,
            'longitude' => 55.0,
            'speed' => 80.0,
            'ignition_status' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'received_at' => now()->toDateTimeString(),
        ];

        $job = new ProcessGpsPing($payload);
        $job->handle();

        $this->assertDatabaseHas('vehicle_location_logs', [
            'vehicle_id' => $vehicle->id,
            'speed' => 80.0,
            'ignition_status' => 1
        ]);

        Event::assertDispatched(VehicleLocationUpdated::class, function ($event) use ($payload) {
            return $event->payload['vehicle_id'] === $payload['vehicle_id'] &&
                $event->payload['company_id'] === $payload['company_id'];
        });
    }
}
