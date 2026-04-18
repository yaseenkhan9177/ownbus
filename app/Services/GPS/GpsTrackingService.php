<?php

namespace App\Services\GPS;

use App\Models\Company;
use App\Models\Vehicle;
use App\Models\VehicleLocation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * GPS Tracking Service — Fleet State Aggregator
 *
 * Sits between the raw VehicleLocation / Vehicle data and the dashboard.
 * Never returns raw DB records — always returns structured arrays
 * safe for JSON / Blade consumption.
 *
 * Performance: uses cache-first reads on per-vehicle positions.
 */
class GpsTrackingService
{
    const CACHE_TTL_SECONDS = 30; // Match dashboard poll interval

    // ─────────────────────────────────────────────────────────────
    // Primary Methods
    // ─────────────────────────────────────────────────────────────

    /**
     * Full fleet state for the live map.
     * Returns all GPS-equipped + active vehicles with their latest position.
     */
    public function getFleetState(Company $company): array
    {
        $cacheKey = "gps_fleet_state:{$company->id}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($company) {
            $vehicles = Vehicle::whereIn('status', ['rented', 'available', 'active'])
                ->get(['id', 'vehicle_number', 'name', 'status', 'tracking_status', 'last_gps_ping_at', 'telematics_device_id']);

            return $vehicles->map(function (Vehicle $vehicle) {
                $location = $this->getLatestPosition($vehicle->id);

                return [
                    'vehicle_id'      => $vehicle->id,
                    'vehicle_number'  => $vehicle->vehicle_number,
                    'name'            => $vehicle->name ?? $vehicle->vehicle_number,
                    'fleet_status'    => $vehicle->status,         // rented/available
                    'tracking_status' => $vehicle->tracking_status ?? 'unknown', // live/offline/unknown
                    'last_ping'       => $vehicle->last_gps_ping_at?->diffForHumans() ?? 'Never',
                    'has_gps'         => !is_null($vehicle->telematics_device_id),
                    'lat'             => $location['lat'] ?? null,
                    'lng'             => $location['lng'] ?? null,
                    'speed'           => $location['speed'] ?? 0,
                    'heading'         => $location['heading'] ?? 0,
                    'last_location_at' => $location['updated_at'] ?? null,
                ];
            })->values()->toArray();
        });
    }

    /**
     * Aggregated KPIs for the dashboard GPS strip.
     */
    public function getGpsKpis(Company $company): array
    {
        $vehicles = Vehicle::whereNotNull('telematics_device_id')
            ->selectRaw('tracking_status, COUNT(*) as cnt')
            ->groupBy('tracking_status')
            ->get()
            ->keyBy('tracking_status');

        $live    = (int) ($vehicles['live']?->cnt    ?? 0);
        $offline = (int) ($vehicles['offline']?->cnt ?? 0);
        $unknown = (int) ($vehicles['unknown']?->cnt ?? 0);
        $total   = $live + $offline + $unknown;

        return [
            'live'          => $live,
            'offline'       => $offline,
            'unknown'       => $unknown,
            'total_tracked' => $total,
            'online_pct'    => $total > 0 ? round(($live / $total) * 100) : 0,
            'rag'           => $offline > 0 ? 'amber' : ($live > 0 ? 'green' : 'unknown'),
        ];
    }

    /**
     * Vehicles currently marked offline — for alert panel.
     */
    public function getOfflineVehicles(Company $company): array
    {
        return Vehicle::where('tracking_status', 'offline')
            ->whereNotNull('telematics_device_id')
            ->get(['id', 'vehicle_number', 'name', 'last_gps_ping_at', 'status'])
            ->map(fn($v) => [
                'vehicle_id'     => $v->id,
                'vehicle_number' => $v->vehicle_number,
                'last_seen'      => $v->last_gps_ping_at?->diffForHumans() ?? 'Never',
                'fleet_status'   => $v->status,
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Get latest position for a vehicle — cache-first.
     */
    protected function getLatestPosition(int $vehicleId): ?array
    {
        // Try hot cache first (written by GpsIngestionController on every ping)
        $cached = Cache::get("vehicle_location:{$vehicleId}");
        if ($cached) {
            return $cached;
        }

        // Fall back to DB cold read
        $loc = VehicleLocation::latestFor($vehicleId);
        if (!$loc) {
            return null;
        }

        return [
            'lat'        => (float) $loc->lat,
            'lng'        => (float) $loc->lng,
            'speed'      => (float) $loc->speed,
            'heading'    => (int)   $loc->heading,
            'updated_at' => $loc->recorded_at->toDateTimeString(),
        ];
    }
}
