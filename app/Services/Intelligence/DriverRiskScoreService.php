<?php

namespace App\Services\Intelligence;

use App\Models\Driver;
use App\Models\VehicleFine;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DriverRiskScoreService
{
    /**
     * Calculate risk score and level for a driver.
     * Weights:
     * - Speeding: 30%
     * - Harsh Driving: 20%
     * - Fines: 20%
     * - Compliance: 20%
     * - Geo Violations: 10%
     */
    public function calculate(Driver $driver): array
    {
        $lookbackDays = 30;
        $startDate = now()->subDays($lookbackDays);

        // 1. Speeding (30%)
        $speedScore = $this->calculateSpeedScore($driver, $startDate);

        // 2. Harsh Driving (20%)
        $harshScore = $this->calculateHarshDrivingScore($driver, $startDate);

        // 3. Fines (20%)
        $finesScore = $this->calculateFinesScore($driver);

        // 4. Compliance (20%)
        $complianceScore = $this->calculateComplianceScore($driver);

        // 5. Geo Violations (10%)
        $geoScore = $this->calculateGeoScore($driver, $startDate);

        // Weighted Total
        $totalScore = (
            ($speedScore * 0.30) +
            ($harshScore * 0.20) +
            ($finesScore * 0.20) +
            ($complianceScore * 0.20) +
            ($geoScore * 0.10)
        );

        $totalScore = round($totalScore);

        // Compliance override: If license is expired, automatic High Risk (Score < 60)
        if ($driver->license_expiry_date && $driver->license_expiry_date->isPast()) {
            $totalScore = min($totalScore, 40); // Force into High Risk
        }

        return [
            'score' => $totalScore,
            'level' => $this->determineRiskLevel($totalScore),
            'breakdown' => [
                'speed' => $speedScore,
                'harsh' => $harshScore,
                'fines' => $finesScore,
                'compliance' => $complianceScore,
                'geo' => $geoScore
            ]
        ];
    }

    protected function calculateSpeedScore(Driver $driver, Carbon $startDate): int
    {
        $count = DB::connection('tenant')->table('telematics_alerts')
            ->where('alert_type', 'Speeding')
            ->whereIn('vehicle_id', function ($query) use ($driver) {
                $query->select('vehicle_id')
                    ->from('rentals')
                    ->where('driver_id', $driver->id);
            })
            ->where('created_at', '>=', $startDate)
            ->count();

        if ($count == 0) return 100;
        if ($count <= 3) return 85;
        if ($count <= 7) return 70;
        if ($count <= 10) return 40;

        return max(0, 40 - (($count - 10) * 5));
    }

    protected function calculateHarshDrivingScore(Driver $driver, Carbon $startDate): int
    {
        $count = DB::connection('tenant')->table('telematics_alerts')
            ->whereIn('alert_type', ['Harsh Braking', 'Harsh Acceleration'])
            ->whereIn('vehicle_id', function ($query) use ($driver) {
                $query->select('vehicle_id')
                    ->from('rentals')
                    ->where('driver_id', $driver->id);
            })
            ->where('created_at', '>=', $startDate)
            ->count();

        if ($count == 0) return 100;
        return max(0, 100 - ($count * 10));
    }

    protected function calculateFinesScore(Driver $driver): int
    {
        $unpaidFines = VehicleFine::where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->count();

        $totalFines = VehicleFine::where('driver_id', $driver->id)->count();

        $penalty = ($unpaidFines * 10) + (max(0, $totalFines - 3) * 5);

        return max(0, 100 - min(40, $penalty));
    }

    protected function calculateComplianceScore(Driver $driver): int
    {
        $score = 100;

        // UAE Compliance specific logic
        if ($driver->license_expiry_date && $driver->license_expiry_date->isPast()) {
            return 0; // Absolute zero if license expired
        }

        if ($driver->rta_permit_expiry && $driver->rta_permit_expiry->diffInDays(now()) < 7) {
            $score -= 30;
        }

        if ($driver->visa_expiry && $driver->visa_expiry->diffInDays(now()) < 14) {
            $score -= 15;
        }

        return max(0, $score);
    }

    protected function calculateGeoScore(Driver $driver, Carbon $startDate): int
    {
        $count = DB::connection('tenant')->table('telematics_alerts')
            ->where('alert_type', 'Geofence')
            ->whereIn('vehicle_id', function ($query) use ($driver) {
                $query->select('vehicle_id')
                    ->from('rentals')
                    ->where('driver_id', $driver->id);
            })
            ->where('created_at', '>=', $startDate)
            ->count();

        if ($count == 0) return 100;
        return max(0, 100 - ($count * 15));
    }

    protected function determineRiskLevel(int $score): string
    {
        if ($score >= 80) return 'low';
        if ($score >= 60) return 'medium';
        return 'high';
    }
}
