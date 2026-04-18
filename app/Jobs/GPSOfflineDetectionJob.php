<?php

namespace App\Jobs;

use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * GPS Offline Detection Job
 *
 * Runs every 5 minutes (scheduled).
 * Marks vehicles 'offline' if their last GPS ping is > 5 minutes ago.
 * Logs alert that can be surfaced on the dashboard.
 */
class GPSOfflineDetectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const OFFLINE_THRESHOLD_MINUTES = 5;

    public function handle(): void
    {
        $threshold = now()->subMinutes(self::OFFLINE_THRESHOLD_MINUTES);

        // Mark offline: GPS-equipped vehicles rented but ping stale > 5 mins
        $offlineVehicles = Vehicle::whereNotNull('telematics_device_id')
            ->where('status', 'rented')
            ->where('tracking_status', 'live')
            ->where(function ($q) use ($threshold) {
                $q->where('last_gps_ping_at', '<', $threshold)
                    ->orWhereNull('last_gps_ping_at');
            })
            ->get();

        foreach ($offlineVehicles as $vehicle) {
            $vehicle->update(['tracking_status' => 'offline']);
            Log::warning("GPS Offline: Vehicle {$vehicle->vehicle_number} (ID:{$vehicle->id}) — last ping: {$vehicle->last_gps_ping_at}");
        }

        // Mark unknown: non-GPS-equipped vehicles
        Vehicle::whereNull('telematics_device_id')
            ->where('tracking_status', '!=', 'unknown')
            ->update(['tracking_status' => 'unknown']);

        if ($offlineVehicles->count() > 0) {
            Log::info("GPSOfflineDetectionJob: Marked {$offlineVehicles->count()} vehicle(s) offline.");
        }
    }
}
