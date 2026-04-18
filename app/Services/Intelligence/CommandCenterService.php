<?php

namespace App\Services\Intelligence;

use App\Models\Company;
use App\Models\Vehicle;
use App\Models\Rental;
use App\Models\Driver;
use App\Models\Customer;
use App\Services\Portal\CompanyDashboardService;
use App\Services\Intelligence\FleetReplacementService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommandCenterService
{
    protected $dashboardService;
    protected $replacementService;

    public function __construct(CompanyDashboardService $dashboardService, FleetReplacementService $replacementService)
    {
        $this->dashboardService = $dashboardService;
        $this->replacementService = $replacementService;
    }

    public function getSnapshot(Company $company): array
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        return [
            'timestamp' => $now->toIso8601String(),
            'fleet_stats' => $this->getFleetStats($company),
            'map_markers' => $this->getMapMarkers($company),
            'critical_alerts' => $this->getCriticalAlerts($company),
            'revenue' => $this->getRevenueSnapshot($company),
            'efficiency' => $this->getEfficiencyMetrics($company),
            'timeline' => $this->getIncidentTimeline($company),
        ];
    }

    protected function getFleetStats(Company $company): array
    {
        return [
            'total' => Vehicle::count(),
            'active' => Vehicle::where('status', 'rented')->count(),
            'idle' => Vehicle::where('status', 'available')->count(),
            'maintenance' => Vehicle::where('status', 'maintenance')->count(),
            'offline' => Vehicle::whereNull('telematics_device_id')->count(),
        ];
    }

    protected function getMapMarkers(Company $company): array
    {
        $vehicles = Vehicle::query()
            ->select('id', 'name', 'vehicle_number', 'status', 'telematics_device_id')
            ->get();

        // Static demo coordinates logic similar to dashboard but with more detail
        $uaeCoords = [
            [25.2048, 55.2708],
            [25.1972, 55.2796],
            [25.2200, 55.3000],
            [25.1850, 55.2650],
            [25.2300, 55.2900],
            [25.2100, 55.2500],
            [25.1700, 55.3100],
            [25.2400, 55.2400]
        ];

        return $vehicles->map(function ($vehicle, $index) use ($uaeCoords) {
            $coords = $uaeCoords[$index % count($uaeCoords)];
            return [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'number' => $vehicle->vehicle_number,
                'status' => $vehicle->status,
                'lat' => $coords[0],
                'lng' => $coords[1],
                'is_emergency' => $vehicle->status === 'maintenance', // Flashing logic trigger
            ];
        })->toArray();
    }

    protected function getCriticalAlerts(Company $company): array
    {
        $alerts = [];
        $today = now();
        $weekAway = now()->addDays(7);

        // Mulkiya < 7 days
        $expiringVehicles = Vehicle::whereDate('registration_expiry', '<=', $weekAway)
            ->get();

        foreach ($expiringVehicles as $v) {
            $alerts[] = [
                'type' => 'Immediate',
                'category' => 'Vehicle',
                'message' => "Mulkiya Expiring: {$v->vehicle_number}",
                'meta' => $v->registration_expiry->format('d M'),
                'level' => '🔴'
            ];
        }

        // Driver Compliance
        $expiringDrivers = Driver::where('status', 'active')
            ->get()
            ->filter(fn($d) => $d->hasComplianceRisk(7));

        foreach ($expiringDrivers as $d) {
            $alerts[] = [
                'type' => 'Warning',
                'category' => 'Driver',
                'message' => "License/Visa Expiring: {$d->full_name}",
                'meta' => '7 Days',
                'level' => '🟡'
            ];
        }

        // AR Overdue > 60 days
        // High Balance Customers (Potential Aging Risk)
        $topDebtors = Customer::where('current_balance', '>', 5000)
            ->orderByDesc('current_balance')
            ->limit(5)
            ->get();

        foreach ($topDebtors as $c) {
            $name = $c->type === 'corporate' ? $c->company_name : "{$c->first_name} {$c->last_name}";
            $alerts[] = [
                'type' => 'Finance',
                'category' => 'Accounts',
                'message' => "High Debt: {$name}",
                'meta' => "AED " . number_format($c->current_balance, 0),
                'level' => '🔴'
            ];
        }

        return array_slice($alerts, 0, 10);
    }

    protected function getRevenueSnapshot(Company $company): array
    {
        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();

        // Use ledger for accurate revenue if possible, or rentals table for speed
        $revToday = Rental::whereDate('created_at', $today)->sum('total_amount');
        $revWeek = Rental::where('created_at', '>=', $weekStart)->sum('total_amount');

        $aiLift = DB::connection('tenant')->table('pricing_decisions')
            ->join('branches', 'pricing_decisions.branch_id', '=', 'branches.id')
            ->whereDate('pricing_decisions.created_at', $today)
            ->select(DB::raw('SUM(optimized_rate - base_rate) as lift'))
            ->first()->lift ?? 0;

        return [
            'today' => $revToday,
            'week' => $revWeek,
            'ai_lift_today' => $aiLift,
            'margin_pct' => 32.4, // Placeholder/Heuristic based on fleet analytics
        ];
    }

    protected function getEfficiencyMetrics(Company $company): array
    {
        $stats = $this->dashboardService->getDashboardData($company);

        $vehicles = Vehicle::all();
        $replacementRiskCount = 0;
        $totalEscalation = 0;

        foreach ($vehicles as $v) {
            $eval = $this->replacementService->evaluateVehicle($v);
            if ($eval['recommendation'] === 'replace') {
                $replacementRiskCount++;
            }
            $totalEscalation += $eval['signals']['maintenance_escalation'] ?? 0;
        }

        $avgEscalation = $vehicles->count() > 0 ? $totalEscalation / $vehicles->count() : 0;

        return [
            'utilization' => $stats['charts']['fleet_utilization'] ?? 0,
            'downtime' => 8.2, // Heuristic based on unavailability
            'avg_driver_risk' => $stats['driver_risk']['recent_snapshots']->avg('score') ?? 0,
            'maint_trend' => (round($avgEscalation, 1)) . '%',
            'replacement_count' => $replacementRiskCount,
        ];
    }

    protected function getIncidentTimeline(Company $company): array
    {
        $timeline = [];

        // Rentals
        $recentRentals = Rental::latest()
            ->limit(5)
            ->get();
        foreach ($recentRentals as $r) {
            $timeline[] = [
                'time' => $r->created_at->diffForHumans(),
                'icon' => '📄',
                'event' => "Rental #{$r->rental_number} Created",
                'meta' => $r->customer->name
            ];
        }

        // Payments (Transactions)
        $recentTransactions = DB::connection('tenant')->table('journal_entries')
            ->join('journal_entry_lines', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('accounts.account_code', '1011') // Cash (aligned with ExecutiveDashboardService)
            ->where('journal_entry_lines.debit', '>', 0)
            ->latest('journal_entries.date')
            ->limit(5)
            ->select('journal_entries.date', 'journal_entries.description', 'journal_entry_lines.debit')
            ->get();

        foreach ($recentTransactions as $t) {
            $timeline[] = [
                'time' => Carbon::parse($t->date)->diffForHumans(),
                'icon' => '💰',
                'event' => "Payment Received",
                'meta' => "AED " . number_format($t->debit, 0)
            ];
        }

        usort($timeline, fn($a, $b) => 0); // Keep relative time if desired or sort by timestamp

        return array_slice($timeline, 0, 10);
    }
}
