<?php

namespace App\Jobs\Telematics;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use App\Events\Telematics\VehicleLocationUpdated;

class ProcessGpsPing implements ShouldQueue
{
    use Queueable;

    public array $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Insert historical data
        DB::connection('tenant')->table('vehicle_location_logs')->insert([
            'company_id' => $this->payload['company_id'],
            'vehicle_id' => $this->payload['vehicle_id'],
            'latitude' => $this->payload['latitude'],
            'longitude' => $this->payload['longitude'],
            'speed' => $this->payload['speed'],
            'ignition_status' => $this->payload['ignition_status'],
            'timestamp' => $this->payload['timestamp'],
            'created_at' => $this->payload['received_at'],
            // heading and fuel_level can be added here if/when they are provided in webhook payload
        ]);

        // 2. Broadcast Event for Live Dashboard
        broadcast(new VehicleLocationUpdated($this->payload));

        // 3. Geofencing Logic (MVP Phase 1)
        $this->processGeofencing();
    }

    /**
     * Process entry and exit events for geofences.
     */
    protected function processGeofencing(): void
    {
        $vehicleId = $this->payload['vehicle_id'];
        $companyId = $this->payload['company_id'];
        $lat = $this->payload['latitude'];
        $lng = $this->payload['longitude'];

        // Determine which geofences the vehicle is currently inside
        // Using the scope defined in the Geofence model
        $currentGeofenceIds = \App\Models\Geofence::where('company_id', $companyId)
            ->where('is_active', true)
            ->containsCoordinate($lat, $lng)
            ->pluck('id')
            ->toArray();

        // Get the previous geofences from Redis
        $redisKey = "vehicle:{$vehicleId}:geofences";
        $previousParams = \Illuminate\Support\Facades\Redis::get($redisKey);
        $previousGeofenceIds = $previousParams ? json_decode($previousParams, true) : [];

        // Compare to find Entered and Exited geofences
        $entered = array_diff($currentGeofenceIds, $previousGeofenceIds);
        $exited = array_diff($previousGeofenceIds, $currentGeofenceIds);

        foreach ($entered as $geofenceId) {
            $this->logAlert($companyId, $vehicleId, $geofenceId, 'Entered', $lat, $lng);
        }

        foreach ($exited as $geofenceId) {
            $this->logAlert($companyId, $vehicleId, $geofenceId, 'Exited', $lat, $lng);
        }

        // Update Redis state
        \Illuminate\Support\Facades\Redis::set($redisKey, json_encode($currentGeofenceIds));
    }

    /**
     * Log a Geofence Alert
     */
    protected function logAlert($companyId, $vehicleId, $geofenceId, $action, $lat, $lng): void
    {
        $geofence = \App\Models\Geofence::find($geofenceId);
        if (!$geofence) return;

        DB::connection('tenant')->table('telematics_alerts')->insert([
            'company_id' => $companyId,
            'vehicle_id' => $vehicleId,
            'alert_type' => 'Geofence',
            'message' => "Vehicle {$vehicleId} {$action} Geofence: {$geofence->name}",
            'latitude' => $lat,
            'longitude' => $lng,
            'created_at' => now(),
        ]);
    }
}
