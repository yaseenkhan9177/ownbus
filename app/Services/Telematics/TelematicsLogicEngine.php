<?php

namespace App\Services\Telematics;

use App\Models\Vehicle;
use App\Models\Geofence;
use App\Models\User;
use App\Models\Rental;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class TelematicsLogicEngine
{
    public function __construct(protected NotificationService $notificationService) {}

    /**
     * Process a single telematics payload for alerts.
     */
    public function process(array $payload): void
    {
        $this->checkSpeeding($payload);
        $this->checkIdling($payload);
        $this->checkGeofence($payload);
        $this->checkHarshDriving($payload);
    }

    /**
     * Violation: Speeding (> 120 km/h)
     */
    protected function checkSpeeding(array $payload): void
    {
        if ($payload['speed'] > 120) {
            $this->logAlert($payload, 'Speeding', "Vehicle exceeded 120 km/h limit (Current: {$payload['speed']} km/h)");
        }
    }

    /**
     * Violation: Excessive Idling (Stationary with ignition ON for > 30 mins)
     */
    protected function checkIdling(array $payload): void
    {
        if (($payload['ignition'] ?? false) && ($payload['speed'] ?? 0) == 0) {
            $lastIdlingKey = "vehicle:{$payload['vehicle_id']}:idling_start";
            $startTime = Redis::get($lastIdlingKey);

            if (!$startTime) {
                Redis::set($lastIdlingKey, time());
            } else {
                $idlingSeconds = time() - $startTime;
                if ($idlingSeconds > 1800) { // 30 mins
                    $this->logAlert($payload, 'Idling', "Vehicle has been idling for " . round($idlingSeconds / 60) . " minutes.");
                    Redis::del($lastIdlingKey);
                }
            }
        } else {
            Redis::del("vehicle:{$payload['vehicle_id']}:idling_start");
        }
    }

    /**
     * Violation: Harsh Driving
     */
    protected function checkHarshDriving(array $payload): void
    {
        $lastSpeedKey = "vehicle:{$payload['vehicle_id']}:last_speed";
        $lastTimestampKey = "vehicle:{$payload['vehicle_id']}:last_timestamp";

        $lastSpeed = Redis::get($lastSpeedKey);
        $lastTimestamp = Redis::get($lastTimestampKey);

        if ($lastSpeed !== null && $lastTimestamp !== null) {
            $currentSpeed = $payload['speed'];
            $currentTime = time();
            $timeDiff = $currentTime - $lastTimestamp;

            if ($timeDiff > 0 && $timeDiff <= 5) {
                $speedDiff = $currentSpeed - $lastSpeed;
                $acceleration = $speedDiff / $timeDiff;

                if ($acceleration > 12) { 
                    $this->logAlert($payload, 'Harsh Acceleration', "Vehicle accelerated rapidly: +{$speedDiff} km/h in {$timeDiff}s");
                } elseif ($acceleration < -12) {
                    $this->logAlert($payload, 'Harsh Braking', "Vehicle braked harshly: {$speedDiff} km/h in {$timeDiff}s");
                }
            }
        }

        Redis::set($lastSpeedKey, $payload['speed']);
        Redis::set($lastTimestampKey, time());
    }

    /**
     * Violation: Geofence breach
     */
    protected function checkGeofence(array $payload): void
    {
        $lat = $payload['latitude'] ?? null;
        $lng = $payload['longitude'] ?? null;

        if (!$lat || !$lng) return;

        // Check if there are any active geofences
        $hasActiveGeofences = Geofence::where('is_active', true)->exists();
        if (!$hasActiveGeofences) return;

        // Check if the coordinate is within ANY active geofence
        $isInside = Geofence::where('is_active', true)
            ->containsCoordinate($lat, $lng)
            ->exists();

        if (!$isInside) {
            $this->logAlert($payload, 'Geofence', "Vehicle moved outside its assigned operational boundary.");
        }
    }

    /**
     * Log alert and Trigger Notifications
     */
    protected function logAlert(array $payload, string $type, string $message): void
    {
        $throttleKey = "alert_throttle:{$payload['vehicle_id']}:{$type}";
        if (Redis::set($throttleKey, 1, 'EX', 600, 'NX')) { // 10 min throttle
            
            // 1. Record in Database
            DB::connection('tenant')->table('telematics_alerts')->insert([
                'vehicle_id'      => $payload['vehicle_id'],
                'alert_type'      => $type,
                'message'         => $message,
                'severity'        => in_array($type, ['Geofence', 'Speeding']) ? 'high' : 'medium',
                'latitude'        => $payload['latitude'] ?? 0,
                'longitude'       => $payload['longitude'] ?? 0,
                'resolved_status' => 'pending',
                'created_at'      => now(),
            ]);

            // 2. Fetch Vehicle & Company context
            $vehicle = Vehicle::find($payload['vehicle_id']);
            if (!$vehicle) return;

            // 3. Notify Company Staff (Owner, Operations Manager)
            $staff = User::whereIn('role', ['Owner', 'Operations Manager', 'company_admin'])
                ->get();
            
            $fullMessage = "🚨 [{$type}] ALERT | Vehicle: {$vehicle->vehicle_number} | {$message}";

            foreach ($staff as $user) {
                // Mock notification via service
                $this->notificationService->sendExpiryNotification($user, "TELEMATICS ALERT", 0); // Reusing service for demo
                \Illuminate\Support\Facades\Log::warning("TELEMATICS NOTIFICATION to {$user->email}: {$fullMessage}");
            }

            // 4. Notify Active Driver if current trip exists
            $activeRental = Rental::where('vehicle_id', $vehicle->id)
                ->where('status', 'active')
                ->with('driver.user')
                ->first();

            if ($activeRental && $activeRental->driver && $activeRental->driver->user) {
                $driverUser = $activeRental->driver->user;
                \Illuminate\Support\Facades\Log::info("TELEMATICS NOTIFICATION to Driver {$driverUser->name}: Caution! {$message}");
            }
        }
    }
}

