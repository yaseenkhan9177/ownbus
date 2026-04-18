<?php

namespace App\Services;

use App\Models\Company;
use App\Models\MaintenanceRecord;
use App\Models\Rental;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Fleet Efficiency Intelligence Service
 *
 * Calculates per-vehicle and fleet-wide efficiency metrics:
 * - Revenue per KM
 * - Maintenance Cost per KM
 * - Fuel Cost per KM (if tracked)
 * - Idle Vehicle %
 */
class FleetEfficiencyService
{
    public function getEfficiencyReport(Company $company): array
    {
        $vehicles = Vehicle::all();
        $total    = $vehicles->count();

        if ($total === 0) {
            return $this->emptyReport();
        }

        $idleCount  = $vehicles->whereIn('status', ['available'])->count();
        $rentalKms  = $this->getTotalRentalKms($company);
        $revenue    = $this->getTotalRevenue($company);
        $maintCost  = $this->getTotalMaintenanceCost($company);

        $revenuePerKm = $rentalKms > 0 ? round($revenue / $rentalKms, 2) : null;
        $maintPerKm   = $rentalKms > 0 ? round($maintCost / $rentalKms, 2) : null;
        $idlePct      = round(($idleCount / $total) * 100, 1);

        // Monthly trend (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyRevenue[] = [
                'month'   => $date->format('M Y'),
                'revenue' => Rental::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('final_amount'),
            ];
        }

        // Per-vehicle breakdown (top performers by revenue)
        $perVehicle = $this->getPerVehicleStats($company);

        return [
            'total_vehicles'         => $total,
            'idle_count'             => $idleCount,
            'idle_pct'               => $idlePct,
            'total_rental_kms'       => $rentalKms,
            'total_revenue'          => round($revenue, 2),
            'total_maintenance_cost' => round($maintCost, 2),
            'revenue_per_km'         => $revenuePerKm,
            'maintenance_cost_per_km' => $maintPerKm,
            'monthly_revenue_trend'  => $monthlyRevenue,
            'per_vehicle'            => $perVehicle,
        ];
    }

    /**
     * Total odometer KMs driven across all completed rentals.
     * When odometer data isn't tracked, estimates from rental days × avg KM/day.
     */
    protected function getTotalRentalKms(Company $company): float
    {
        // Try real odometer data (end_odometer - start_odometer)
        $tracked = DB::connection('tenant')->table('rentals')
            ->whereIn('status', ['completed', 'active'])
            ->whereNotNull('odometer_end')
            ->selectRaw('SUM(odometer_end - odometer_start) as total_km')
            ->value('total_km');

        if ($tracked > 0) return (float) $tracked;

        // Fallback: estimate 120 KM/day per rental
        $rentalDays = DB::connection('tenant')->table('rentals')
            ->whereIn('status', ['completed', 'active'])
            ->selectRaw('SUM(DATEDIFF(end_date, start_date)) as total_days')
            ->value('total_days') ?? 0;

        return (float) $rentalDays * 120;
    }

    protected function getTotalRevenue(Company $company): float
    {
        return (float) Rental::whereIn('status', ['completed', 'active'])
            ->sum('final_amount');
    }

    protected function getTotalMaintenanceCost(Company $company): float
    {
        return (float) DB::connection('tenant')->table('maintenance_records')
            ->where('status', 'completed')
            ->sum('total_cost');
    }

    protected function getPerVehicleStats(Company $company): \Illuminate\Support\Collection
    {
        return Vehicle::withCount(['rentals as completed_rentals' => fn($q) => $q->where('status', 'completed')])
            ->withSum(['rentals as total_revenue' => fn($q) => $q->whereIn('status', ['completed', 'active'])], 'final_amount')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get(['id', 'name', 'vehicle_number', 'status'])
            ->map(fn($v) => [
                'vehicle_number'    => $v->vehicle_number,
                'name'              => $v->name,
                'status'            => $v->status,
                'completed_rentals' => $v->completed_rentals ?? 0,
                'total_revenue'     => round($v->total_revenue ?? 0, 2),
            ]);
    }

    protected function emptyReport(): array
    {
        return [
            'total_vehicles' => 0,
            'idle_count' => 0,
            'idle_pct' => 0,
            'total_rental_kms' => 0,
            'total_revenue' => 0,
            'total_maintenance_cost' => 0,
            'revenue_per_km' => null,
            'maintenance_cost_per_km' => null,
            'monthly_revenue_trend' => [],
            'per_vehicle' => [],
        ];
    }
}
