<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Vehicle;
use App\Models\VehicleUnavailability;
use Illuminate\Pagination\LengthAwarePaginator;

class MaintenanceRepository
{
    /**
     * Get paginated maintenance logs.
     */
    public function getMaintenanceLogs(Company $company, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = VehicleUnavailability::with('vehicle');

        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['status'])) {
            $now = now();
            if ($filters['status'] === 'active') {
                $query->where('start_datetime', '<=', $now)
                    ->where('end_datetime', '>=', $now);
            } elseif ($filters['status'] === 'upcoming') {
                $query->where('start_datetime', '>', $now);
            } elseif ($filters['status'] === 'completed') {
                $query->where('end_datetime', '<', $now);
            }
        }

        if (!empty($filters['reason_type'])) {
            $query->where('reason_type', $filters['reason_type']);
        }

        return $query->orderBy('start_datetime', 'desc')->paginate($perPage);
    }

    /**
     * Get vehicles nearing service intervals.
     */
    public function getUpcomingMaintenance(Company $company, int $thresholdKm = 1000)
    {
        return Vehicle::where('status', '!=', 'retired')
            ->whereRaw('(next_service_odometer - current_odometer) <= ?', [$thresholdKm])
            ->whereRaw('(next_service_odometer - current_odometer) > 0') // Not already overdue/negative
            ->orderByRaw('(next_service_odometer - current_odometer) ASC')
            ->take(10)
            ->get();
    }
}
