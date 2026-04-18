<?php

namespace App\Services\Intelligence;

use App\Models\Vehicle;
use App\Models\JournalEntry;
use App\Models\Rental;
use App\Models\VehicleUnavailability;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FleetReplacementService
{
    /**
     * Evaluate a vehicle's replacement suitability based on 5 indicators.
     * Weights:
     * - Maintenance Escalation: 30%
     * - Downtime Ratio: 20%
     * - Margin Decline: 25%
     * - Revenue Gap: 15%
     * - Age Factor: 10%
     */
    public function evaluateVehicle(Vehicle $vehicle): array
    {
        // 1. Maintenance Escalation (30%)
        $maintEscalation = $this->calculateMaintenanceEscalation($vehicle);

        // 2. Downtime Ratio (20%)
        $downtimeRatio = $this->calculateDowntimeRatio($vehicle);

        // 3. Margin Decline (25%)
        $marginDecline = $this->calculateMarginDecline($vehicle);

        // 4. Revenue Gap (15%)
        $revenueGap = $this->calculateRevenueGap($vehicle);

        // 5. Age Factor (10%)
        $ageFactor = $this->calculateAgeFactor($vehicle);

        // Weighted Score Calculation (0-100)
        // Note: For escalation and downtime, higher value means higher replacement pressure.
        // We normalize these to 0-100 where 100 is "high replacement need".

        $totalScore = (
            ($maintEscalation * 0.30) +
            ($downtimeRatio * 0.20) +
            ($marginDecline * 0.25) +
            ($revenueGap * 0.15) +
            ($ageFactor * 0.10)
        );

        $totalScore = (int) round($totalScore);

        return [
            'replacement_score' => $totalScore,
            'recommendation' => $this->determineRecommendation($totalScore),
            'signals' => [
                'maintenance_escalation' => round($maintEscalation, 1),
                'downtime_ratio' => round($downtimeRatio, 1),
                'margin_decline' => round($marginDecline, 1),
                'revenue_gap' => round($revenueGap, 1),
                'age_factor' => round($ageFactor, 1)
            ]
        ];
    }

    protected function calculateMaintenanceEscalation(Vehicle $vehicle): float
    {
        // Get maintenance costs (Account 5012)
        $maintCosts = DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.vehicle_id', $vehicle->id)
            ->where('accounts.account_code', '5012')
            ->where('journal_entries.is_posted', true)
            ->orderBy('journal_entries.date', 'asc')
            ->select('journal_entry_lines.debit', 'journal_entries.date')
            ->get();

        if ($maintCosts->count() < 4) return 0; // Not enough history

        $firstThreeAvg = $maintCosts->take(3)->avg('debit');
        $lastThreeAvg = $maintCosts->take(-3)->avg('debit');

        if ($firstThreeAvg <= 0) return 0;

        $increasePct = (($lastThreeAvg - $firstThreeAvg) / $firstThreeAvg) * 100;

        // Normalize: 35% increase = 70 points, 50%+ = 100 points
        return min(100, max(0, ($increasePct / 50) * 100));
    }

    protected function calculateDowntimeRatio(Vehicle $vehicle): float
    {
        $lookbackDays = 180; // 6 months
        $startDate = now()->subDays($lookbackDays);

        $downtimeMinutes = VehicleUnavailability::where('vehicle_id', $vehicle->id)
            ->where('start_datetime', '>=', $startDate)
            ->where('reason_type', 'maintenance')
            ->get()
            ->sum(function ($u) {
                return $u->start_datetime->diffInMinutes($u->end_datetime ?? now());
            });

        $totalMinutes = $lookbackDays * 24 * 60;
        $ratio = ($downtimeMinutes / $totalMinutes) * 100;

        // Normalize: 15% downtime = 100 points
        return min(100, ($ratio / 15) * 100);
    }

    protected function calculateMarginDecline(Vehicle $vehicle): float
    {
        // Calculate margin for each of the last 6 months
        $margins = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();

            $revenue = $this->getMonthlyRevenue($vehicle, $start, $end);
            $costs = $this->getMonthlyCosts($vehicle, $start, $end);

            $margins[] = $revenue - $costs;
        }

        // Check trend
        $declineCount = 0;
        for ($i = 1; $i < count($margins); $i++) {
            if ($margins[$i] < $margins[$i - 1]) $declineCount++;
        }

        // 5 consecutive declines = 100 points
        return ($declineCount / 5) * 100;
    }

    protected function calculateRevenueGap(Vehicle $vehicle): float
    {
        $now = now();
        $start = $now->copy()->subMonths(3)->startOfMonth();

        // Vehicle Revenue/KM
        $vehicleRevenue = $this->getMonthlyRevenue($vehicle, $start, $now);
        $vehicleKm = $this->getKmDriven($vehicle, $start, $now);
        $vehicleRevPerKm = $vehicleKm > 0 ? $vehicleRevenue / $vehicleKm : 0;

        // Fleet Average Revenue/KM
        $fleetRevenue = DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('accounts.account_code', '4010')
            ->whereBetween('journal_entries.date', [$start->toDateString(), $now->toDateString()])
            ->sum('journal_entry_lines.credit');

        $fleetKm = Rental::whereIn('vehicle_id', function ($q) {
            $q->select('id')->from('vehicles');
        })
            ->whereBetween('start_date', [$start, $now])
            ->sum(DB::raw('odometer_end - odometer_start'));

        $fleetAvgRevPerKm = $fleetKm > 0 ? $fleetRevenue / $fleetKm : 5.0; // Default 5 AED/km

        if ($vehicleRevPerKm >= $fleetAvgRevPerKm) return 0;

        $gapPct = (($fleetAvgRevPerKm - $vehicleRevPerKm) / $fleetAvgRevPerKm) * 100;

        // 25% gap = 100 points
        return min(100, ($gapPct / 25) * 100);
    }

    protected function calculateAgeFactor(Vehicle $vehicle): float
    {
        if (!$vehicle->purchase_date) return 0;

        $years = $vehicle->purchase_date->diffInYears(now());
        $targetLife = $vehicle->type === 'bus' ? 5 : 7;

        if ($years < $targetLife) return 0;

        $pressure = (($years - $targetLife) / 2) * 100; // 2 years over target = 100 points
        return min(100, max(0, $pressure));
    }

    protected function determineRecommendation(int $score): string
    {
        if ($score >= 80) return 'replace';
        if ($score >= 60) return 'monitor';
        return 'retain';
    }

    private function getMonthlyRevenue($vehicle, $start, $end): float
    {
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.vehicle_id', $vehicle->id)
            ->where('accounts.account_code', '4010')
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->sum('journal_entry_lines.credit');
    }

    private function getMonthlyCosts($vehicle, $start, $end): float
    {
        // Maintenance + Fuel
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.vehicle_id', $vehicle->id)
            ->whereIn('accounts.account_code', ['5012', '5011'])
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->sum('journal_entry_lines.debit');
    }

    private function getKmDriven($vehicle, $start, $end): float
    {
        return (float) Rental::where('vehicle_id', $vehicle->id)
            ->whereBetween('start_date', [$start, $end])
            ->sum(DB::raw('odometer_end - odometer_start'));
    }
}
