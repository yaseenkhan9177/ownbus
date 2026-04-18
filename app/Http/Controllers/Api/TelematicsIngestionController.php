<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GpsDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class TelematicsIngestionController extends Controller
{
    /**
     * Ingest high-speed GPS pings.
     * POST /api/v1/telematics/ping
     */
    public function ping(Request $request)
    {
        // 1. Minimum Validation (Speed over exhaustive checks)
        $data = $request->validate([
            'imei' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'speed' => 'required|numeric',
            'engine_status' => 'required|boolean', // Mapped to ignition_status
            'timestamp' => 'required|integer',     // Client generated payload timestamp
        ]);

        // Note: For production we would add signature validation (skipping for Phase 1 testing)
        // e.g. md5(imei + timestamp + secret)

        // 2. Authenticate IMEI & Get Cache Context (Company/Vehicle)
        // We use Cache here to avoid a DB hit every 10 seconds.
        $device = Cache::remember("telematics:device:{$data['imei']}", 3600, function () use ($data) {
            return GpsDevice::where('imei_number', $data['imei'])
                ->where('status', 'active')
                ->first(['id', 'company_id', 'vehicle_id']);
        });

        if (!$device || !$device->vehicle_id) {
            return response()->json(['error' => 'Device not registered or inactive'], 404);
        }

        // 3. Format Payload for Job
        $payload = [
            'company_id' => $device->company_id,
            'vehicle_id' => $device->vehicle_id,
            'imei' => $data['imei'],
            'latitude' => $data['lat'],
            'longitude' => $data['lng'],
            'speed' => $data['speed'],
            'ignition_status' => $data['engine_status'],
            'timestamp' => date('Y-m-d H:i:s', $data['timestamp']), // Assuming Unix Timestamp
            'received_at' => now()->toDateTimeString(),
        ];

        // 4. Update "Hot" Storage (Redis) for Live Dashboard
        // Live location update (overwrites previous)
        Redis::set("vehicle:{$device->vehicle_id}:location", json_encode($payload));

        // 5. Dispatch Queue Worker
        \App\Jobs\Telematics\ProcessGpsPing::dispatch($payload);

        // 6. Respond instantly
        return response()->json(['status' => 'ok'], 202);
    }
}
