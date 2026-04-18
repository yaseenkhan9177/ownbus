<?php

namespace App\Services\Intelligence;

use App\Models\VehicleFine;
use App\Models\Contract;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardService
{
    protected $profitability;
    protected $driverStats;
    protected $maintenance;
    protected $financials;

    public function __construct(
        VehicleProfitabilityService $profitability,
        DriverPerformanceService $driverStats,
        MaintenanceIntelligenceService $maintenance,
        FinancialReportService $financials
    ) {
        $this->profitability = $profitability;
        $this->driverStats = $driverStats;
        $this->maintenance = $maintenance;
        $this->financials = $financials;
    }

    /**
     * Get aggregated KPIs for the executive dashboard.
     */
    public function getDashboardStats(int $companyId, ?int $branchId = null): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        return [
            'financial' => $this->getFinancialKpis($companyId, $branchId),
            'fleet_performance' => $this->getFleetKpis($companyId, $branchId, $startOfMonth, $endOfMonth),
            'contracts' => $this->getContractKpis($companyId, $branchId),
            'traffic_compliance' => $this->getFineKpis($companyId, $branchId),
            'expenses' => $this->getExpenseKpis($companyId, $branchId),
            'revenue_trend' => $this->getRevenueTrend($companyId, $branchId),
            'maintenance_alerts' => $this->getMaintenanceAlerts($companyId, $branchId),
        ];
    }

    /**
     * Traffic Compliance & Fine RisK.
     */
    protected function getFineKpis(int $companyId, ?int $branchId): array
    {
        $pendingFines = VehicleFine::where('status', 'pending')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        $totalFineAmount = VehicleFine::where('status', 'pending')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $overdueFines = VehicleFine::where('status', 'pending')
            ->whereDate('due_date', '<', now())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        $vehiclesWithFines = VehicleFine::where('status', 'pending')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->distinct('vehicle_id')
            ->count('vehicle_id');

        // Top 5 Vehicles with Fines (for the compliance panel)
        $topFinedVehicles = VehicleFine::where('status', 'pending')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('vehicle_id', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as fine_count'))
            ->groupBy('vehicle_id')
            ->orderByDesc('total_amount')
            ->with('vehicle:id,name,vehicle_number')
            ->take(5)
            ->get();

        // Top 5 Drivers with Fines (Worst Drivers)
        $topFinedDrivers = VehicleFine::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('driver_id', DB::raw('SUM(amount) as total_amount'), DB::raw('SUM(black_points) as total_points'))
            ->groupBy('driver_id')
            ->orderByDesc('total_amount')
            ->with('driver:id,first_name,last_name')
            ->take(5)
            ->get();

        return [
            'pending_count' => $pendingFines,
            'total_amount' => (float)$totalFineAmount,
            'overdue_count' => $overdueFines,
            'vehicles_at_risk' => $vehiclesWithFines,
            'top_vehicles' => $topFinedVehicles->map(fn($f) => [
                'vehicle' => $f->vehicle->vehicle_number . ' - ' . $f->vehicle->name,
                'amount' => (float)$f->total_amount,
                'count' => $f->fine_count
            ])->toArray(),
            'worst_drivers' => $topFinedDrivers->map(fn($f) => [
                'name' => $f->driver ? $f->driver->name : 'Unknown',
                'amount' => (float)$f->total_amount,
                'points' => (int)$f->total_points
            ])->toArray(),
        ];
    }

    /**
     * Expense Analytics & Operational Costs.
     */
    protected function getExpenseKpis(int $companyId, ?int $branchId): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        $fuelCost = DB::connection('tenant')->table('expenses')
            ->where('category', 'fuel')
            ->whereBetween('expense_date', [$startOfMonth, $now])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $maintenanceCost = DB::connection('tenant')->table('expenses')
            ->where('category', 'maintenance')
            ->whereBetween('expense_date', [$startOfMonth, $now])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $totalExpensesToday = DB::connection('tenant')->table('expenses')
            ->where('expense_date', $now->toDateString())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        // Cost per KM logic (Top 5 Costliest Vehicles)
        // Note: Needs current_odometer vs start_odometer or similar mapping
        // For now, heuristic using total expenses / total KM driven
        $topCostVehicles = DB::connection('tenant')->table('expenses')
            ->join('vehicles', 'vehicles.id', '=', 'expenses.vehicle_id')
            ->select(
                'expenses.vehicle_id',
                'vehicles.name',
                'vehicles.vehicle_number',
                DB::raw('SUM(total_amount) as total_expenses')
            )
            ->groupBy('expenses.vehicle_id', 'vehicles.name', 'vehicles.vehicle_number')
            ->orderByDesc('total_expenses')
            ->take(5)
            ->get();

        return [
            'fuel_this_month' => (float)$fuelCost,
            'maintenance_this_month' => (float)$maintenanceCost,
            'total_today' => (float)$totalExpensesToday,
            'top_cost_vehicles' => $topCostVehicles->map(fn($v) => [
                'vehicle' => $v->vehicle_number . ' - ' . $v->name,
                'amount' => (float)$v->total_expenses,
            ])->toArray(),
        ];
    }

    /**
     * Contract Pipeline & Revenue Visibility.
     */
    protected function getContractKpis(int $companyId, ?int $branchId): array
    {
        $activeContracts = Contract::where('status', 'active')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        $expiringContracts = Contract::where('status', 'active')
            ->whereDate('end_date', '<=', now()->addDays(30))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        $monthlyContractRevenue = Contract::where('status', 'active')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('monthly_rate');

        $totalVehicles = Vehicle::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        $allocatedVehicles = Contract::where('status', 'active')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->distinct('vehicle_id')
            ->count('vehicle_id');

        return [
            'active_contracts' => $activeContracts,
            'expiring_soon' => $expiringContracts,
            'monthly_revenue' => (float)$monthlyContractRevenue,
            'allocation_rate' => $totalVehicles > 0 ? round(($allocatedVehicles / $totalVehicles) * 100, 2) : 0,
        ];
    }

    /**
     * AR, AP, and Cash Balance from Ledger.
     */
    protected function getFinancialKpis(int $companyId, ?int $branchId): array
    {
        // AR (Asset, code 1013)
        $arBalance = $this->getAccountGroupBalance($companyId, '1013', $branchId);
        // AP (Liability, code 2011)
        $apBalance = $this->getAccountGroupBalance($companyId, '2011', $branchId);
        // Cash (Asset, code 1011)
        $cashBalance = $this->getAccountGroupBalance($companyId, '1011', $branchId);

        return [
            'ar_outstanding' => (float)$arBalance,
            'ap_outstanding' => (float)$apBalance,
            'cash_balance' => (float)$cashBalance,
        ];
    }

    /**
     * Fleet-wide profitability and performance.
     */
    protected function getFleetKpis(int $companyId, ?int $branchId, Carbon $start, Carbon $end): array
    {
        $vehicles = Vehicle::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $totalRevenue = 0;
        $totalProfit = 0;
        $topVehicles = [];

        foreach ($vehicles as $vehicle) {
            $report = $this->profitability->getVehicleReport($vehicle, $start, $end);
            $totalRevenue += $report['revenue'];
            $totalProfit += $report['profit'];
            $topVehicles[] = $report;
        }

        usort($topVehicles, fn($a, $b) => $b['profit'] <=> $a['profit']);

        return [
            'monthly_revenue' => $totalRevenue,
            'monthly_profit' => $totalProfit,
            'top_performing_vehicles' => array_slice($topVehicles, 0, 5),
        ];
    }

    /**
     * Last 6 months revenue trend.
     */
    protected function getRevenueTrend(int $companyId, ?int $branchId): array
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            // Revenue = Income accounts (Credit-normal)
            $revenue = (float) DB::connection('tenant')->table('journal_entry_lines')
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
                ->where('journal_entries.is_posted', true)
                ->where('accounts.account_type', 'income')
                ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
                ->when($branchId, fn($q) => $q->where('journal_entries.branch_id', $branchId))
                ->sum('journal_entry_lines.credit');

            $data[] = $revenue;
            $labels[] = $month->format('M Y');
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    protected function getMaintenanceAlerts(int $companyId, ?int $branchId): array
    {
        return [
            'due_now' => Vehicle::where('status', 'maintenance')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->count(),
            'upcoming' => Vehicle::whereColumn('current_odometer', '>=', DB::raw('next_service_odometer - 500'))
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->count(),
        ];
    }

    protected function getAccountGroupBalance(int $companyId, string $code, ?int $branchId): float
    {
        $account = Account::where('account_code', $code)
            ->first();

        if (!$account) return 0;

        // Cumulative balance (inception to now)
        $totals = DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $account->id)
            ->where('journal_entries.is_posted', true)
            ->when($branchId, fn($q) => $q->where('journal_entries.branch_id', $branchId))
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $balance = (float)($totals->total_debit ?? 0) - (float)($totals->total_credit ?? 0);

        // Normalize based on type
        return in_array($account->account_type, ['asset', 'expense']) ? $balance : -$balance;
    }
}
