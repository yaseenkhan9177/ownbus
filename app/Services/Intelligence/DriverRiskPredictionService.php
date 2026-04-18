<?php

namespace App\Services\Intelligence;

use App\Models\Driver;
use App\Models\Rental;
use App\Models\VehicleFine;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DriverRiskPredictionService
{
    protected $riskScoreService;

    public function __construct(DriverRiskScoreService $riskScoreService)
    {
        $this->riskScoreService = $riskScoreService;
    }

    /**
     * Predict accident risk for a driver.
     * Weights:
     * - Driving behavior (Telematics): 35%
     * - Past violations (Fines): 20%
     * - Fatigue index (Shift patterns): 15%
     * - Compliance risk: 15%
     * - Branch safety context: 15%
     */
    public function predictAccidentRisk(Driver $driver): array
    {
        $signals = [];
        $baseRisk = $this->riskScoreService->calculate($driver);
        
        // 1. Driving Behavior (Harsh events + Speeding)
        $behaviorScore = (
            ($baseRisk['breakdown']['harsh'] * 0.6) + 
            ($baseRisk['breakdown']['speed'] * 0.4)
        );
        $signals['behavior_score'] = $behaviorScore;

        // 2. Past Violations
        $violationScore = $baseRisk['breakdown']['fines'];
        $signals['violation_score'] = $violationScore;

        // 3. Fatigue Index (Shift duration / Overtime)
        $fatigueScore = $this->calculateFatigueScore($driver);
        $signals['fatigue_index'] = $fatigueScore;

        // 4. Compliance Risk
        $complianceScore = $baseRisk['breakdown']['compliance'];
        $signals['compliance_score'] = $complianceScore;

        // 5. Branch Safety Context (Placeholder or average of branch drivers)
        $branchScore = 80; // Heuristic
        $signals['branch_safety'] = $branchScore;

        // Calculate Weighted Risk (0-100 where 0 is safest, 100 is highest accident risk)
        // Note: baseRisk scores are 100=safest, so we invert.
        $accidentRisk = (
            ((100 - $behaviorScore) * 0.35) +
            ((100 - $violationScore) * 0.20) +
            ((100 - $fatigueScore) * 0.15) +
            ((100 - $complianceScore) * 0.15) +
            ((100 - $branchScore) * 0.15)
        );

        $accidentRisk = (int) round($accidentRisk);

        return [
            'risk_score' => $accidentRisk,
            'probability_60_days' => round($accidentRisk / 100, 2),
            'risk_level' => $this->determineRiskLevel($accidentRisk),
            'signals' => $signals
        ];
    }

    protected function calculateFatigueScore(Driver $driver): int
    {
        // Check last 7 days rental durations
        $sevenDaysAgo = now()->subDays(7);
        $totalMinutes = Rental::where('driver_id', $driver->id)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->get()
            ->sum(function($r) {
                $start = $r->actual_pickup_date ?? $r->pickup_date;
                $end = $r->actual_return_date ?? $r->return_date ?? now();
                return $start ? $start->diffInMinutes($end) : 0;
            });

        $avgHoursPerDay = ($totalMinutes / 60) / 7;

        if ($avgHoursPerDay > 12) return 20; // High fatigue (safest=100)
        if ($avgHoursPerDay > 10) return 50;
        if ($avgHoursPerDay > 8) return 80;
        
        return 100;
    }

    protected function determineRiskLevel(int $score): string
    {
        if ($score >= 70) return 'high';
        if ($score >= 40) return 'medium';
        return 'low';
    }
}
