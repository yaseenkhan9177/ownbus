<?php

namespace App\Services\Intelligence;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Rental;
use App\Models\ContractInvoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchBenchmarkService
{
    /**
     * Analyze and rank all branches within a company.
     */
    public function analyzeCompany(Company $company): array
    {
        $branches = Branch::all();
        $branchStats = [];

        foreach ($branches as $branch) {
            $branchStats[] = $this->analyzeBranch($branch);
        }

        // Sort by overall score descending
        usort($branchStats, fn($a, $b) => $b['score'] <=> $a['score']);

        return [
            'branches' => $branchStats,
            'best_branch' => $branchStats[0] ?? null,
            'worst_branch' => end($branchStats) ?: null,
            'avg_fleet_score' => count($branchStats) > 0 ? array_sum(array_column($branchStats, 'score')) / count($branchStats) : 0,
        ];
    }

    /**
     * Calculate 7 core metrics for a specific branch.
     * Weights (Matching Phase 7J specification):
     * - Financial Performance (30%): Revenue 15%, Margin 15%
     * - Operational Efficiency (25%): Utilization 15%, Maintenance 10%
     * - Compliance Health: 15%
     * - Driver Safety: 15%
     * - AR Efficiency: 15%
     */
    public function analyzeBranch(Branch $branch): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        // 1. Revenue Performance (15%) - 0-100 score
        $revenue = $this->getBranchRevenue($branch, $startOfMonth, $now);
        $revenueScore = min(100, ($revenue / 500000) * 100);

        // 2. Profit Margin (15%)
        $expenses = $this->getBranchExpenses($branch, $startOfMonth, $now);
        $marginPercent = $revenue > 0 ? (($revenue - $expenses) / $revenue) * 100 : 0;
        $marginScore = min(100, max(0, ($marginPercent / 30) * 100));

        // 3. Fleet Utilization (15%)
        $utilizationPercent = $this->getBranchUtilization($branch, $startOfMonth, $now);
        $utilizationScore = $utilizationPercent;

        // 4. Maintenance Efficiency (10%)
        $maintPerKm = $this->getBranchMaintenancePerKm($branch, $startOfMonth, $now);
        $maintenanceScore = min(100, max(0, 100 - (($maintPerKm - 2.0) / 3.0) * 100));

        // 5. Driver Safety (15%)
        $avgDriverRisk = $this->getBranchAverageDriverRisk($branch);
        $driverSafetyScore = $avgDriverRisk;

        // 6. AR Efficiency (15%)
        $arRatio = $this->getBranchARAgingRatio($branch);
        $arScore = min(100, max(0, 100 - ($arRatio / 30) * 100));

        // 7. Compliance Health (15%)
        $complianceScore = $this->getBranchComplianceScore($branch);

        // Weighted Overall Score
        $totalScore = (
            ($revenueScore * 0.15) +
            ($marginScore * 0.15) +
            ($utilizationScore * 0.15) +
            ($maintenanceScore * 0.10) +
            ($driverSafetyScore * 0.15) +
            ($arScore * 0.15) +
            ($complianceScore * 0.15)
        );

        return [
            'branch_id' => $branch->id,
            'name' => $branch->name,
            'revenue' => round($revenue, 2),
            'margin_percent' => round($marginPercent, 1),
            'utilization_percent' => round($utilizationPercent, 1),
            'maintenance_cost_per_km' => round($maintPerKm, 2),
            'driver_risk_avg' => round($avgDriverRisk, 1),
            'ar_aging_ratio' => round($arRatio, 1),
            'compliance_score' => round($complianceScore, 1),
            'score' => (int) round($totalScore),
            'breakdown' => [
                'revenue' => round($revenueScore, 1),
                'margin' => round($marginScore, 1),
                'utilization' => round($utilizationScore, 1),
                'maintenance' => round($maintenanceScore, 1),
                'risk' => round($driverSafetyScore, 1),
                'ar' => round($arScore, 1),
                'compliance' => round($complianceScore, 1),
            ]
        ];
    }

    protected function getBranchRevenue(Branch $branch, Carbon $start, Carbon $end): float
    {
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.branch_id', $branch->id)
            ->where('accounts.account_code', '4010')
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->sum('journal_entry_lines.credit');
    }

    protected function getBranchExpenses(Branch $branch, Carbon $start, Carbon $end): float
    {
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.branch_id', $branch->id)
            ->whereIn('accounts.account_code', ['5011', '5012', '5013', '5014', '5015'])
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->sum('journal_entry_lines.debit');
    }

    protected function getBranchUtilization(Branch $branch, Carbon $start, Carbon $end): float
    {
        $totalDays = $start->diffInDays($end) ?: 1;
        $vehicleCount = Vehicle::where('branch_id', $branch->id)->count() ?: 1;
        $capacity = $totalDays * $vehicleCount;

        $rentalDays = Rental::whereIn('vehicle_id', function ($q) use ($branch) {
            $q->select('id')->from('vehicles')->where('branch_id', $branch->id);
        })
            ->whereBetween('start_date', [$start, $end])
            ->whereIn('status', ['active', 'completed'])
            ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1'));

        return min(100, ($rentalDays / $capacity) * 100);
    }

    protected function getBranchMaintenancePerKm(Branch $branch, Carbon $start, Carbon $end): float
    {
        $maintCost = (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.branch_id', $branch->id)
            ->where('accounts.account_code', '5012')
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->sum('journal_entry_lines.debit');

        $kmDriven = (float) Rental::whereIn('vehicle_id', function ($q) use ($branch) {
            $q->select('id')->from('vehicles')->where('branch_id', $branch->id);
        })
            ->whereBetween('start_date', [$start, $end])
            ->sum(DB::raw('odometer_end - odometer_start'));

        return $kmDriven > 0 ? $maintCost / $kmDriven : 0;
    }

    protected function getBranchAverageDriverRisk(Branch $branch): float
    {
        return (float) DB::connection('tenant')->table('driver_risk_snapshots')
            ->join('drivers', 'driver_risk_snapshots.driver_id', '=', 'drivers.id')
            ->where('drivers.branch_id', $branch->id)
            ->whereIn('driver_risk_snapshots.id', function ($q) {
                $q->select(DB::raw('MAX(id)'))->from('driver_risk_snapshots')->groupBy('driver_id');
            })
            ->avg('score') ?: 100; // Default to 100 (safe) if no data
    }

    protected function getBranchARAgingRatio(Branch $branch): float
    {
        $totalOutstanding = ContractInvoice::whereIn('contract_id', function ($q) use ($branch) {
            $q->select('id')->from('contracts')->where('branch_id', $branch->id);
        })
            ->whereIn('status', ['draft', 'partial'])
            ->sum('total_amount');

        if ($totalOutstanding <= 0) return 0;

        $overdue = ContractInvoice::whereIn('contract_id', function ($q) use ($branch) {
            $q->select('id')->from('contracts')->where('branch_id', $branch->id);
        })
            ->whereIn('status', ['draft', 'partial'])
            ->where('due_date', '<', now()->toDateString())
            ->sum('total_amount');

        return ($overdue / $totalOutstanding) * 100;
    }

    protected function getBranchComplianceScore(Branch $branch): float
    {
        $vehicles = Vehicle::where('branch_id', $branch->id)->get();
        $drivers = Driver::where('branch_id', $branch->id)->get();

        $totalItems = $vehicles->count() + $drivers->count();
        if ($totalItems <= 0) return 100;

        $riskItems = 0;
        foreach ($vehicles as $v) {
            if ($v->hasComplianceRisk()) $riskItems++;
        }
        foreach ($drivers as $d) {
            if ($d->hasComplianceRisk()) $riskItems++;
        }

        return (($totalItems - $riskItems) / $totalItems) * 100;
    }
}
