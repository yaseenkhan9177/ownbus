<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * GPS Ingestion Controller
 *
 * Receives real-time telemetry from GPS hardware devices.
 * High-throughput endpoint — minimal processing, maximum speed.
 *
 * Authentication: device_token checked against vehicle.telematics_device_id
 * Route: POST /api/gps/ping
 */
class GpsIngestionController extends Controller
{
    public function ping(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_token' => 'required|string',
            'lat'          => 'required|numeric|between:-90,90',
            'lng'          => 'required|numeric|between:-180,180',
            'speed'        => 'nullable|numeric|min:0|max:300',
            'heading'      => 'nullable|integer|between:0,360',
            'accuracy'     => 'nullable|numeric|min:0',
            'recorded_at'  => 'nullable|date',
        ]);

        // Look up vehicle by device token
        $vehicle = Cache::remember("gps_device:{$data['device_token']}", 300, function () use ($data) {
            return Vehicle::where('telematics_device_id', $data['device_token'])->first();
        });

        if (!$vehicle) {
            return response()->json(['error' => 'Unknown device token'], 401);
        }

        $recordedAt = isset($data['recorded_at']) ? $data['recorded_at'] : now()->toDateTimeString();

        // Store location
        VehicleLocation::create([
            'vehicle_id'  => $vehicle->id,
            'lat'         => $data['lat'],
            'lng'         => $data['lng'],
            'speed'       => $data['speed'] ?? null,
            'heading'     => $data['heading'] ?? null,
            'accuracy'    => $data['accuracy'] ?? null,
            'source'      => 'device',
            'recorded_at' => $recordedAt,
        ]);

        // Update vehicle tracking status
        $vehicle->update([
            'tracking_status' => 'live',
            'last_gps_ping_at' => now(),
        ]);

        // Cache latest location for dashboard fast-read
        Cache::put("vehicle_location:{$vehicle->id}", [
            'lat'         => $data['lat'],
            'lng'         => $data['lng'],
            'speed'       => $data['speed'] ?? 0,
            'heading'     => $data['heading'] ?? 0,
            'updated_at'  => now()->toDateTimeString(),
        ], 600); // 10 min cache

        return response()->json([
            'status'     => 'ok',
            'vehicle_id' => $vehicle->id,
            'timestamp'  => $recordedAt,
        ]);
    }

    /**
     * Get latest location for a single vehicle (for dashboard AJAX).
     * Route: GET /api/gps/vehicle/{vehicle}
     */
    public function latestLocation(Vehicle $vehicle): JsonResponse
    {
        // Try cache first
        $cached = Cache::get("vehicle_location:{$vehicle->id}");
        if ($cached) {
            return response()->json(array_merge($cached, ['source' => 'cache', 'vehicle_id' => $vehicle->id]));
        }

        // Fallback to DB
        $location = VehicleLocation::latestFor($vehicle->id);
        if (!$location) {
            return response()->json(['error' => 'No location data'], 404);
        }

        return response()->json([
            'vehicle_id'  => $vehicle->id,
            'lat'         => $location->lat,
            'lng'         => $location->lng,
            'speed'       => $location->speed,
            'heading'     => $location->heading,
            'updated_at'  => $location->recorded_at->toDateTimeString(),
            'source'      => 'database',
        ]);
    }

    /**
     * Fleet-wide live map snapshot — all vehicles for a company in one call.
     * Dashboard polls this every 30 seconds for Leaflet marker updates.
     *
     * Route: GET /api/v1/gps/fleet/{companyId}
     * Auth:  auth:sanctum
     */
    public function fleetSnapshot(int $companyId): JsonResponse
    {
        $vehicles = Vehicle::whereNotNull('telematics_device_id')
            ->get(['id', 'vehicle_number', 'name', 'status', 'tracking_status', 'last_gps_ping_at']);

        $snapshot = $vehicles->map(function (Vehicle $vehicle) {
            // Prefer cache (sub-millisecond), fall back to DB
            $loc = Cache::get("vehicle_location:{$vehicle->id}");

            if (!$loc) {
                $dbLoc = VehicleLocation::latestFor($vehicle->id);
                if ($dbLoc) {
                    $loc = [
                        'lat'        => $dbLoc->lat,
                        'lng'        => $dbLoc->lng,
                        'speed'      => $dbLoc->speed,
                        'heading'    => $dbLoc->heading,
                        'updated_at' => $dbLoc->recorded_at->toDateTimeString(),
                    ];
                }
            }

            return [
                'vehicle_id'       => $vehicle->id,
                'vehicle_number'   => $vehicle->vehicle_number,
                'name'             => $vehicle->name,
                'status'           => $vehicle->status,
                'tracking_status'  => $vehicle->tracking_status,
                'last_ping'        => $vehicle->last_gps_ping_at?->toDateTimeString(),
                'location'         => $loc, // null if no GPS data
            ];
        });

        return response()->json([
            'company_id'   => $companyId,
            'vehicle_count' => $vehicles->count(),
            'generated_at' => now()->toDateTimeString(),
            'vehicles'     => $snapshot,
        ]);
    }
}
