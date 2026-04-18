<?php

namespace App\Services\Intelligence;

use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleRiskPredictionService
{
    protected $replacementService;

    public function __construct(FleetReplacementService $replacementService)
    {
        $this->replacementService = $replacementService;
    }

    /**
     * Predict breakdown risk for a vehicle.
     * Weights:
     * - Maintenance frequency: 20%
     * - Cost acceleration: 20%
     * - Downtime trend: 15%
     * - KM overdue service: 15%
     * - Age factor: 10%
     * - Replacement score: 10%
     * - Harsh driving events: 10%
     */
    public function predictBreakdownRisk(Vehicle $vehicle): array
    {
        $signals = [];
        $weights = [
            'frequency' => 0.20,
            'acceleration' => 0.20,
            'downtime' => 0.15,
            'km_overdue' => 0.15,
            'age' => 0.10,
            'replacement' => 0.10,
            'harsh' => 0.10
        ];

        // 1. Maintenance frequency (last 6 months)
        $freqScore = $this->calculateFrequencyScore($vehicle);
        $signals['maintenance_frequency'] = $freqScore;

        // 2. Cost acceleration (0-100)
        $eval = $this->replacementService->evaluateVehicle($vehicle);
        $accelScore = $eval['signals']['maintenance_escalation'] ?? 0;
        $signals['cost_acceleration'] = $accelScore;

        // 3. Downtime trend (0-100)
        $downtimeScore = $eval['signals']['downtime_ratio'] ?? 0;
        $signals['downtime_trend'] = $downtimeScore;

        // 4. KM overdue service
        $kmOverdueScore = $this->calculateKmOverdueScore($vehicle);
        $signals['km_overdue'] = $kmOverdueScore;

        // 5. Age factor
        $ageScore = $eval['signals']['age_factor'] ?? 0;
        $signals['age_factor'] = $ageScore;

        // 6. Replacement score
        $replacementScore = $eval['replacement_score'] ?? 0;
        $signals['replacement_score'] = $replacementScore;

        // 7. Harsh driving events (last 30 days)
        $harshScore = $this->calculateHarshDrivingScore($vehicle);
        $signals['harsh_driving'] = $harshScore;

        $totalScore = (
            ($freqScore * $weights['frequency']) +
            ($accelScore * $weights['acceleration']) +
            ($downtimeScore * $weights['downtime']) +
            ($kmOverdueScore * $weights['km_overdue']) +
            ($ageScore * $weights['age']) +
            ($replacementScore * $weights['replacement']) +
            ($harshScore * $weights['harsh'])
        );

        $totalScore = (int) round($totalScore);

        return [
            'risk_score' => $totalScore,
            'risk_level' => $this->determineRiskLevel($totalScore),
            'probability_next_30_days' => round($totalScore / 100, 2),
            'signals' => $signals
        ];
    }

    protected function calculateFrequencyScore(Vehicle $vehicle): int
    {
        $sixMonthsAgo = now()->subMonths(6);
        $count = MaintenanceRecord::where('vehicle_id', $vehicle->id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->count();

        if ($count >= 6) return 100; // Average 1/month is high for predictive breakdown
        if ($count >= 4) return 75;
        if ($count >= 2) return 40;
        return 10;
    }

    protected function calculateKmOverdueScore(Vehicle $vehicle): int
    {
        if (!$vehicle->next_service_odometer) return 0;

        $overdue = $vehicle->current_odometer - $vehicle->next_service_odometer;
        if ($overdue <= 0) return 0;

        if ($overdue > 2000) return 100;
        if ($overdue > 1000) return 80;
        if ($overdue > 500) return 50;
        return 30;
    }

    protected function calculateHarshDrivingScore(Vehicle $vehicle): int
    {
        $count = DB::connection('tenant')->table('telematics_alerts')
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('alert_type', ['Harsh Braking', 'Harsh Acceleration'])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($count > 50) return 100;
        if ($count > 20) return 70;
        if ($count > 5) return 40;
        return 0;
    }

    protected function determineRiskLevel(int $score): string
    {
        if ($score >= 75) return 'high';
        if ($score >= 45) return 'medium';
        return 'low';
    }
}
