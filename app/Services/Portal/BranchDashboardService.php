<?php

namespace App\Services\Portal;

use App\Models\Branch;
use App\Models\Vehicle;
use App\Models\Rental;
use App\Models\Driver;
use App\Models\User;
use App\Models\RentalPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BranchDashboardService
{
    /**
     * Get all dashboard data for a branch.
     */
    public function getDashboardData(User $user): array
    {
        $branchId = $user->branch_id;
        $companyId = $user->company_id;

        if (!$branchId) {
            return [];
        }

        return [
            'summary' => $this->getSummaryCards($branchId),
            'active_rentals' => $this->getActiveRentalsTable($branchId),
            'vehicle_status' => $this->getVehicleStatus($branchId),
            'driver_status' => $this->getDriverStatus($branchId),
            'alerts' => $this->getBranchAlerts($branchId, $companyId),
        ];
    }

    /**
     * Get summary KPI cards.
     */
    protected function getSummaryCards(int $branchId): array
    {
        $today = Carbon::today();

        return [
            'today_rentals' => Rental::where('branch_id', $branchId)
                ->whereDate('start_date', $today)
                ->count(),

            'active_rentals' => Rental::where('branch_id', $branchId)
                ->where('status', Rental::STATUS_ACTIVE)
                ->count(),

            'available_vehicles' => Vehicle::where('branch_id', $branchId)
                ->where('status', Vehicle::STATUS_AVAILABLE)
                ->count(),

            'maintenance_vehicles' => Vehicle::where('branch_id', $branchId)
                ->where('status', 'maintenance')
                ->count(),

            'today_revenue' => RentalPayment::whereHas('rental', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
                ->whereDate('paid_at', $today)
                ->sum('amount'),

            'pending_payments' => Rental::where('branch_id', $branchId)
                ->whereIn('status', [Rental::STATUS_ACTIVE, Rental::STATUS_COMPLETED])
                ->where('payment_status', '!=', Rental::PAYSTATUS_PAID)
                ->get()
                ->sum(function ($rental) {
                    return $rental->final_amount - $rental->payments()->sum('amount');
                }),
        ];
    }

    /**
     * Get active rentals table data.
     */
    protected function getActiveRentalsTable(int $branchId): \Illuminate\Support\Collection
    {
        return Rental::where('branch_id', $branchId)
            ->whereIn('status', [Rental::STATUS_ACTIVE, 'overdue'])
            ->with(['customer', 'vehicle', 'driver'])
            ->latest()
            ->limit(10)
            ->get();
    }

    /**
     * Get vehicle status overview.
     */
    protected function getVehicleStatus(int $branchId): array
    {
        $stats = Vehicle::where('branch_id', $branchId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'available' => $stats[Vehicle::STATUS_AVAILABLE] ?? 0,
            'rented' => $stats['rented'] ?? 0,
            'maintenance' => $stats['maintenance'] ?? 0,
            'total' => array_sum($stats),
        ];
    }

    /**
     * Get driver status overview.
     */
    protected function getDriverStatus(int $branchId): array
    {
        $available = Driver::where('branch_id', $branchId)
            ->where('status', Driver::STATUS_ACTIVE)
            ->whereDoesntHave('rentals', function ($q) {
                $q->where('status', Rental::STATUS_ACTIVE);
            })->count();

        $onTrip = Driver::where('branch_id', $branchId)
            ->whereHas('rentals', function ($q) {
                $q->where('status', Rental::STATUS_ACTIVE);
            })->count();

        $licenseExpiring = Driver::where('branch_id', $branchId)
            ->where('license_expiry_date', '<=', Carbon::now()->addDays(30))
            ->where('license_expiry_date', '>=', Carbon::now())
            ->count();

        return [
            'available' => $available,
            'on_trip' => $onTrip,
            'license_expiring' => $licenseExpiring,
        ];
    }

    /**
     * Get operational alerts.
     */
    protected function getBranchAlerts(int $branchId, int $companyId): array
    {
        $alerts = [];
        $now = Carbon::now();

        // 1. Vehicle service due (within 500km)
        $maintenance = Vehicle::where('branch_id', $branchId)
            ->whereRaw('next_service_odometer - current_odometer <= ?', [500])
            ->limit(3)
            ->get();
        foreach ($maintenance as $v) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Service due: {$v->vehicle_number} ({$v->name})",
                'meta' => ($v->next_service_odometer - $v->current_odometer) . " km left",
            ];
        }

        // 2. Driver license expiring (within 30 days)
        $drivers = Driver::where('branch_id', $branchId)
            ->where('license_expiry_date', '<=', $now->copy()->addDays(30))
            ->limit(3)
            ->get();
        foreach ($drivers as $d) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "License Expiring: {$d->name}",
                'meta' => "Expires " . $d->license_expiry_date->format('d M'),
            ];
        }

        // 3. Overdue payments
        $overdueRentals = Rental::where('branch_id', $branchId)
            ->where('status', 'overdue')
            ->limit(3)
            ->get();
        foreach ($overdueRentals as $r) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Overdue Rental: #{$r->rental_number}",
                'meta' => $r->customer->name ?? 'N/A',
            ];
        }

        // 4. Contracts ending soon (within 24 hours)
        $endingSoon = Rental::where('branch_id', $branchId)
            ->where('status', Rental::STATUS_ACTIVE)
            ->whereBetween('end_date', [$now, $now->copy()->addDay()])
            ->limit(3)
            ->get();
        foreach ($endingSoon as $r) {
            $alerts[] = [
                'type' => 'info',
                'message' => "Return Due: #{$r->rental_number}",
                'meta' => $r->end_date->format('H:i'),
            ];
        }

        return $alerts;
    }
}
