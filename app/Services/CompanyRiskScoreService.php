<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Driver;
use App\Models\JournalEntryLine;
use App\Models\Rental;
use App\Models\Vehicle;
use App\Models\VehicleFine;
use Carbon\Carbon;

/**
 * Executive Risk Scoring Engine
 *
 * Returns a composite 0–100 risk score and RAG (Red/Amber/Green) status
 * based on 5 weighted financial and operational risk factors.
 *
 * Designed for boardroom-grade executive dashboards.
 */
class CompanyRiskScoreService
{
    // Thresholds
    const SCORE_GREEN  = 30;  // 0–30  = Healthy
    const SCORE_AMBER  = 65;  // 31–65 = Moderate Risk
    // 66–100 = High Risk

    // Weight map (must sum to 100)
    const WEIGHTS = [
        'maintenance_overdue'   => 25,
        'driver_expiry'         => 20,
        'ar_aging'              => 25,
        'fine_overdue'          => 15,
        'cash_coverage'         => 15,
    ];

    public function getScore(Company $company): array
    {
        $factors = [
            'maintenance_overdue' => $this->maintenanceOverdueRatio($company),
            'driver_expiry'       => $this->driverExpiryRatio($company),
            'ar_aging'            => $this->arAgingRatio($company),
            'fine_overdue'        => $this->fineOverdueRatio($company),
            'cash_coverage'       => $this->cashCoverageRatio($company),
        ];

        // Weighted composite (each factor returns 0–100 penalty score)
        $rawScore = 0;
        foreach ($factors as $key => $penalty) {
            $rawScore += ($penalty / 100) * self::WEIGHTS[$key];
        }
        $score = (int) round(min(100, max(0, $rawScore)));

        $rag = match (true) {
            $score <= self::SCORE_GREEN => 'healthy',
            $score <= self::SCORE_AMBER => 'moderate',
            default                     => 'high_risk',
        };

        return [
            'score'       => $score,
            'rag'         => $rag,
            'label'       => match ($rag) {
                'healthy'   => '🟢 Healthy',
                'moderate'  => '🟡 Moderate Risk',
                'high_risk' => '🔴 High Risk',
            },
            'color'       => match ($rag) {
                'healthy'   => 'emerald',
                'moderate'  => 'amber',
                'high_risk' => 'rose',
            },
            'factors'     => [
                'maintenance_overdue' => ['label' => 'Maintenance Overdue', 'penalty' => round($factors['maintenance_overdue']), 'weight' => self::WEIGHTS['maintenance_overdue']],
                'driver_expiry'       => ['label' => 'Driver Doc Expiry',   'penalty' => round($factors['driver_expiry']),       'weight' => self::WEIGHTS['driver_expiry']],
                'ar_aging'            => ['label' => 'AR Aging > 60d',       'penalty' => round($factors['ar_aging']),            'weight' => self::WEIGHTS['ar_aging']],
                'fine_overdue'        => ['label' => 'Fines Overdue',        'penalty' => round($factors['fine_overdue']),        'weight' => self::WEIGHTS['fine_overdue']],
                'cash_coverage'       => ['label' => 'Cash Coverage',        'penalty' => round($factors['cash_coverage']),       'weight' => self::WEIGHTS['cash_coverage']],
            ],
        ];
    }

    /** % of active vehicles with overdue maintenance (penalty 0–100) */
    protected function maintenanceOverdueRatio(Company $company): float
    {
        $total    = Vehicle::count();
        if ($total === 0) return 0;
        $overdue  = Vehicle::where('status', 'maintenance')->count();
        return ($overdue / $total) * 100;
    }

    /** % of active drivers with any UAE document expiring within 30 days (penalty 0–100) */
    protected function driverExpiryRatio(Company $company): float
    {
        $drivers = Driver::where('status', 'active')->get();
        if ($drivers->isEmpty()) return 0;
        $atRisk  = $drivers->filter(fn($d) => $d->hasComplianceRisk(30))->count();
        return ($atRisk / $drivers->count()) * 100;
    }

    /**
     * Penalty based on AR aging > 60 days.
     * Reads from rentals where payment not received and start_date > 60 days ago.
     */
    protected function arAgingRatio(Company $company): float
    {
        $totalAr = Rental::whereIn('status', ['active', 'overdue', 'completed'])
            ->sum('final_amount');

        if (!$totalAr) return 0;

        $agedAr = Rental::where('payment_status', 'pending')
            ->where('start_date', '<', now()->subDays(60))
            ->sum('final_amount');

        return min(100, ($agedAr / $totalAr) * 100);
    }

    /** % of pending fines that are overdue (past due_date) */
    protected function fineOverdueRatio(Company $company): float
    {
        $total   = VehicleFine::where('status', 'pending')->count();
        if ($total === 0) return 0;
        $overdue = VehicleFine::where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();
        return ($overdue / $total) * 100;
    }

    /**
     * Cash coverage penalty: ratio of total AR to total Cash balance.
     * High AR vs low cash = high risk.
     */
    protected function cashCoverageRatio(Company $company): float
    {
        $cashBalance = JournalEntryLine::whereHas('journalEntry')
            ->whereHas('account', fn($q) => $q->where('account_code', '1011')->orWhere('account_code', '1012'))
            ->selectRaw('SUM(debit) - SUM(credit) as net')
            ->value('net') ?? 0;

        $arBalance = JournalEntryLine::whereHas('journalEntry')
            ->whereHas('account', fn($q) => $q->where('account_code', '1013'))
            ->selectRaw('SUM(debit) - SUM(credit) as net')
            ->value('net') ?? 0;

        // If AR > Cash = risk. Penalty proportional to shortfall.
        if ($cashBalance <= 0 && $arBalance > 0) return 100;
        if ($arBalance <= 0) return 0;
        $coverageRatio = $cashBalance / $arBalance;
        // >1.0 = fully covered = 0 risk. <0.5 = 100% penalty.
        return min(100, max(0, (1 - $coverageRatio) * 100));
    }
}
