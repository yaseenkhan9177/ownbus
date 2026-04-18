<?php

namespace App\Services\Intelligence;

use App\Models\Vehicle;
use App\Models\JournalEntry;
use App\Models\Rental;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FleetAnalyticsService
{
    /**
     * Analyze a single vehicle's performance over a period.
     */
    public function analyzeVehicle(Vehicle $vehicle, Carbon $from, Carbon $to): array
    {
        $kmDriven = $this->getKmDriven($vehicle, $from, $to);

        // Financials from Ledger (using new vehicle_id tag)
        $revenue = $this->getLedgerSum($vehicle->id, '4010', $from, $to, 'credit');
        $maintenance = $this->getLedgerSum($vehicle->id, '5012', $from, $to, 'debit');
        $fuel = $this->getLedgerSum($vehicle->id, '5011', $from, $to, 'debit');

        // Estimations for Insurance (Prorated)
        $daysInPeriod = $from->diffInDays($to) ?: 1;
        $insuranceProrated = $daysInPeriod * 5.00; // Estimated 5 AED/day

        $totalCost = $maintenance + $fuel + $insuranceProrated;
        $grossMargin = $revenue - $totalCost;

        // KM Metrics
        $safeKm = max(1, $kmDriven);
        $revPerKm = $revenue / $safeKm;
        $maintPerKm = $maintenance / $safeKm;
        $fuelPerKm = $fuel / $safeKm;
        $costPerKm = $totalCost / $safeKm;
        $marginPerKm = $grossMargin / $safeKm;

        // Utilization
        $utilization = $this->getUtilizationRate($vehicle, $from, $to);

        return [
            'vehicle_id' => $vehicle->id,
            'name' => $vehicle->name,
            'vehicle_number' => $vehicle->vehicle_number,
            'km_driven' => $kmDriven,
            'revenue' => $revenue,
            'revenue_per_km' => $revPerKm,
            'maint_cost_per_km' => $maintPerKm,
            'fuel_cost_per_km' => $fuelPerKm,
            'total_cost_per_km' => $costPerKm,
            'gross_margin_per_km' => $marginPerKm,
            'utilization_rate' => $utilization,
            'idle_percentage' => 100 - $utilization,
            'roi_score' => $this->calculateRoiScore($marginPerKm, $utilization, $vehicle),
        ];
    }

    /**
     * Aggregate findings for the entire company.
     */
    public function companyOverview(Company $company): array
    {
        $from = now()->startOfMonth();
        $to = now();

        $vehicles = Vehicle::all();
        $fleetData = $vehicles->map(fn($v) => $this->analyzeVehicle($v, $from, $to));

        $avgRevPerKm = $fleetData->avg('revenue_per_km');
        $avgCostPerKm = $fleetData->avg('total_cost_per_km');
        $avgUtilization = $fleetData->avg('utilization_rate');

        $topPerformers = $fleetData->sortByDesc('roi_score')->take(5)->values()->all();
        $bottomPerformers = $fleetData->sortBy('roi_score')->take(5)->values()->all();

        $underperforming = $fleetData->where('utilization_rate', '<', 50)->values()->all();
        $negativeMargin = $fleetData->where('gross_margin_per_km', '<', 0)->values()->all();

        return [
            'avg_revenue_per_km' => $avgRevPerKm,
            'avg_cost_per_km' => $avgCostPerKm,
            'avg_margin_per_km' => $avgRevPerKm - $avgCostPerKm,
            'fleet_utilization_pct' => $avgUtilization,
            'top_vehicles' => $topPerformers,
            'bottom_vehicles' => $bottomPerformers,
            'underperforming' => $underperforming,
            'negative_margin' => $negativeMargin,
            'period_label' => $from->format('M Y'),
        ];
    }

    protected function getKmDriven(Vehicle $vehicle, Carbon $from, Carbon $to): float
    {
        $startOdo = Rental::where('vehicle_id', $vehicle->id)
            ->where('start_date', '>=', $from)
            ->orderBy('start_date', 'asc')
            ->value('odometer_start');

        $endOdo = Rental::where('vehicle_id', $vehicle->id)
            ->where('end_date', '<=', $to)
            ->orderBy('end_date', 'desc')
            ->value('odometer_end');

        if ($startOdo && $endOdo) {
            return max(0, $endOdo - $startOdo);
        }

        // Fallback: Use current odometer vs service interval or estimate
        return (float) Rental::where('vehicle_id', $vehicle->id)
            ->whereBetween('start_date', [$from, $to])
            ->count() * 120.0; // Estimate 120km per rental
    }

    protected function getLedgerSum($vehicleId, $accountCode, $from, $to, $type = 'debit'): float
    {
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.vehicle_id', $vehicleId)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$from->toDateString(), $to->toDateString()])
            ->where('accounts.account_code', $accountCode)
            ->sum("journal_entry_lines.{$type}");
    }

    protected function getUtilizationRate(Vehicle $vehicle, Carbon $from, Carbon $to): float
    {
        $totalDays = $from->diffInDays($to) ?: 1;
        $rentalDays = Rental::where('vehicle_id', $vehicle->id)
            ->whereBetween('start_date', [$from, $to])
            ->whereIn('status', ['completed', 'active'])
            ->sum(DB::raw('DATEDIFF(end_date, start_date)')) ?: 0;

        return min(100, ($rentalDays / $totalDays) * 100);
    }

    protected function calculateRoiScore($marginPerKm, $utilization, Vehicle $vehicle): int
    {
        // Margin component (0-100) -> 6 AED/km is 100
        $marginScore = min(100, max(0, ($marginPerKm / 6) * 100));

        // Utilization component (0-100)
        $utilScore = $utilization;

        // Maintenance health component
        $maintPenalty = 0;
        if ($vehicle->status === 'maintenance') $maintPenalty = 50;
        if ($vehicle->next_service_odometer - $vehicle->current_odometer <= 500) $maintPenalty = 30;
        $maintScore = 100 - $maintPenalty;

        $total = ($marginScore * 0.4) + ($utilScore * 0.4) + ($maintScore * 0.2);

        return (int) round($total);
    }
}
