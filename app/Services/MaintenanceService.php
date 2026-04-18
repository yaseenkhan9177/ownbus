<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\BusUtilizationLog;
use App\Models\MaintenancePrediction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    protected const SERVICE_INTERVAL_KM = 10000;

    /**
     * Run predictions for all active vehicles in a company.
     */
    public function predictServiceNeeds(): void
    {
        $vehicles = Vehicle::where('status', 'available')
            ->get();

        foreach ($vehicles as $vehicle) {
            $this->predictForVehicle($vehicle);
        }
    }

    /**
     * Predict next maintenance date for a specific vehicle.
     */
    public function predictForVehicle(Vehicle $vehicle): ?MaintenancePrediction
    {
        // 1. Calculate Average Daily KM (Last 30 days)
        $avgDailyKm = BusUtilizationLog::where('bus_id', $vehicle->id)
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->avg('km_used') ?: 0;

        if ($avgDailyKm <= 0) {
            // Assume a conservative default if no recent data (e.g., 100km/day)
            $avgDailyKm = 100;
        }

        // 2. Calculate KM to next interval
        $currentOdo = $vehicle->current_odometer;
        $nextInterval = ceil(($currentOdo + 1) / self::SERVICE_INTERVAL_KM) * self::SERVICE_INTERVAL_KM;
        $kmRemaining = $nextInterval - $currentOdo;

        // 3. Project Date
        $daysToService = ceil($kmRemaining / $avgDailyKm);
        $predictedDate = Carbon::now()->addDays($daysToService);

        // 4. Update or Create Prediction
        return MaintenancePrediction::updateOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'status' => 'pending', // Only update if still pending
            ],
            [
                'prediction_type' => 'mileage',
                'predicted_date' => $predictedDate,
                'confidence_score' => $this->calculateConfidence($avgDailyKm),
                'reason' => "Based on avg daily usage of " . round($avgDailyKm, 1) . "km. Estimated $kmRemaining km remaining to " . number_format($nextInterval) . "km service.",
            ]
        );
    }

    /**
     * Confidence is higher if we have more data points.
     */
    protected function calculateConfidence(float $avgKm): int
    {
        // Placeholder for more complex logic
        return $avgKm > 0 ? 85 : 50;
    }
}
