<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleLocation;
use App\Events\BroadcastVehicleLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GpsApiController extends Controller
{
    /**
     * Ingest location data from external GPS devices.
     * Expected JSON: { "imei": "...", "lat": ..., "lng": ..., "speed": ..., "heading": ..., "ignition": bool }
     */
    public function ingest(Request $request)
    {
        $validated = $request->validate([
            'imei' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'ignition' => 'nullable|boolean',
        ]);

        $vehicle = Vehicle::where('gps_imei', $validated['imei'])->first();

        if (!$vehicle) {
            return response()->json(['error' => 'Device not registered'], 404);
        }

        $location = VehicleLocation::create([
            'vehicle_id' => $vehicle->id,
            'latitude' => $validated['lat'],
            'longitude' => $validated['lng'],
            'speed' => $validated['speed'] ?? 0,
            'heading' => $validated['heading'] ?? 0,
            'ignition_status' => $validated['ignition'] ?? false,
            'recorded_at' => now(),
            'source' => 'GPS-Unit',
        ]);

        // Broadcast for live map
        broadcast(new BroadcastVehicleLocation($location));

        // Check geofences (simplified)
        $this->checkGeofences($vehicle, $location);

        return response()->json(['status' => 'success']);
    }

    protected function checkGeofences($vehicle, $location)
    {
        // Placeholder for spatial query logic
        // If outside allowed zone -> send notification
    }
}
