<?php

namespace App\Services\Fleet;

use App\Models\Company;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\DriverProfile;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class FleetAlertService
{
    /**
     * Get active alerts for the company dashboard, segmented by type.
     */
    public function getActiveAlerts(Company $company): array
    {
        return CacheService::rememberTagged(["alerts"], "alerts:active", CacheService::TTL_SHORT, function () use ($company) {

            // Maintenance Alerts
            $maintenanceAlerts = Vehicle::whereRaw('next_service_odometer - current_odometer <= ?', [500])
                ->limit(5)
                ->get()
                ->map(function ($vehicle) {
                    return [
                        'type' => 'warning', // visual type
                        'category' => 'maintenance',
                        'message' => "Vehicle {$vehicle->vehicle_number} requires maintenance soon.",
                        'action_url' => Route::has('portal.fleet.maintenance') ? route('portal.fleet.maintenance', ['vehicle' => $vehicle->id]) : '#',
                    ];
                })->toArray();

            // Driver License Alerts
            $now = Carbon::now();
            $driverAlerts = DriverProfile::whereHas('documents', function ($q) use ($now) {
                $q->where('document_type', 'license')
                    ->where('expiry_date', '<=', $now->copy()->addDays(30));
            })
                ->with('user')
                ->limit(5)
                ->get()
                ->map(function ($profile) {
                    $name = $profile->user ? $profile->user->name : 'Unknown';
                    $id = $profile->user ? $profile->user->id : $profile->id;
                    return [
                        'type' => 'danger',
                        'category' => 'documents',
                        'message' => "Driver {$name}'s license is expiring soon.",
                        'action_url' => Route::has('portal.drivers.show') ? route('portal.drivers.show', $id) : '#',
                    ];
                })->toArray();

            // Payment/Financial Alerts (Placeholder for Late Payments)
            $paymentAlerts = [];
            // Logic for late customer payments would go here

            // Combine and limit total
            $allAlerts = array_merge($maintenanceAlerts, $driverAlerts, $paymentAlerts);

            return array_slice($allAlerts, 0, 10);
        });
    }
}
