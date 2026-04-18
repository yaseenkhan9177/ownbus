<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleMaintenancePrediction;
use App\Models\MaintenanceRecord;
use App\Models\BusUtilizationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PredictiveMaintenanceService
{
    /**
     * Run predictions for all active vehicles.
     */
    public function generatePredictions()
    {
        $vehicles = Vehicle::where('status', '!=', 'inactive')->get();

        foreach ($vehicles as $vehicle) {
            $this->analyzeVehicle($vehicle);
        }
    }

    /**
     * Main method to analyze a vehicle and store prediction snapshot.
     */
    public function analyzeVehicle(Vehicle $vehicle): array
    {
        $avgKmPerDay = $this->calculateAvgKmPerDay($vehicle);
        $intervalKm = $this->calculateMaintenanceInterval($vehicle);

        $currentKm = $vehicle->current_odometer;
        $lastServiceKm = $vehicle->last_service_odometer ?? 0;
        $nextServiceTargetKm = $lastServiceKm + $intervalKm;

        $remainingKm = max(0, $nextServiceTargetKm - $currentKm);

        $predictedDaysRemaining = $avgKmPerDay > 0 ? (int) ceil($remainingKm / $avgKmPerDay) : 999;

        $riskLevel = $this->calculateRiskLevel($predictedDaysRemaining, $vehicle);
        $costGrowth = $this->calculateCostTrend($vehicle);

        // Store Snapshot
        VehicleMaintenancePrediction::updateOrCreate(
            ['vehicle_id' => $vehicle->id],
            [
                'predicted_service_date' => Carbon::now()->addDays($predictedDaysRemaining),
                'risk_level' => $riskLevel,
                'avg_km_per_day' => $avgKmPerDay,
                'cost_growth_percentage' => $costGrowth,
                'interval_km' => $intervalKm,
                'calculated_at' => Carbon::now(),
            ]
        );

        return [
            'predicted_days_remaining' => $predictedDaysRemaining,
            'risk_level' => $riskLevel,
            'interval_km' => $intervalKm,
            'avg_km_per_day' => $avgKmPerDay,
            'cost_growth' => $costGrowth,
        ];
    }

    /**
     * Calculate Average KM per Day (last 30 days).
     */
    protected function calculateAvgKmPerDay(Vehicle $vehicle): float
    {
        $sumKm = BusUtilizationLog::where('bus_id', $vehicle->id)
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->sum('km_used');

        return round($sumKm / 30, 2);
    }

    /**
     * Calculate Maintenance Interval Pattern (Average KM between services).
     */
    protected function calculateMaintenanceInterval(Vehicle $vehicle): int
    {
        $records = MaintenanceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', MaintenanceRecord::STATUS_COMPLETED)
            ->orderBy('odometer_reading', 'desc')
            ->limit(5)
            ->get();

        if ($records->count() < 2) {
            return 5000; // Default interval
        }

        $intervals = [];
        for ($i = 0; $i < $records->count() - 1; $i++) {
            $diff = $records[$i]->odometer_reading - $records[$i + 1]->odometer_reading;
            if ($diff > 0) {
                $intervals[] = $diff;
            }
        }

        return count($intervals) > 0 ? (int) (array_sum($intervals) / count($intervals)) : 5000;
    }

    /**
     * Calculate Risk Level based on days remaining and cost escalation.
     */
    protected function calculateRiskLevel(int $daysRemaining, Vehicle $vehicle): string
    {
        $risk = 'low';

        if ($daysRemaining < 7) {
            $risk = 'high';
        } elseif ($daysRemaining < 21) {
            $risk = 'medium';
        }

        // Cost growth escalation
        $growth = $this->calculateCostTrend($vehicle);
        if ($growth > 30 && $risk === 'medium') {
            $risk = 'high';
        }

        // Usage stress escalation
        if ($this->isVehicleStressed($vehicle)) {
            if ($risk === 'low') $risk = 'medium';
            elseif ($risk === 'medium') $risk = 'high';
        }

        return $risk;
    }

    /**
     * Calculate Cost Trend Growth % over last 3 services.
     */
    protected function calculateCostTrend(Vehicle $vehicle): float
    {
        $costs = MaintenanceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', MaintenanceRecord::STATUS_COMPLETED)
            ->orderBy('completed_date', 'desc')
            ->limit(3)
            ->pluck('total_cost')
            ->toArray();

        if (count($costs) < 2) {
            return 0;
        }

        // Reverse to get chronological order [oldest, ..., newest]
        $costs = array_reverse($costs);

        $totalGrowth = 0;
        for ($i = 1; $i < count($costs); $i++) {
            if ($costs[$i - 1] > 0) {
                $totalGrowth += (($costs[$i] - $costs[$i - 1]) / $costs[$i - 1]) * 100;
            }
        }

        return round($totalGrowth / (count($costs) - 1), 2);
    }

    /**
     * Usage Stress Index logic.
     */
    protected function isVehicleStressed(Vehicle $vehicle): bool
    {
        // High KM/day threshold
        $avgKm = $this->calculateAvgKmPerDay($vehicle);
        if ($avgKm > 300) return true;

        // High fuel consumption (placeholder logic for demo)
        $avgFuel = BusUtilizationLog::where('bus_id', $vehicle->id)
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->avg('fuel_consumed');

        if ($avgFuel > 50) return true; // threshold 50 liters/ping or day

        return false;
    }
}
