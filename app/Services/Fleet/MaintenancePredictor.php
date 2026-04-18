<?php

namespace App\Services\Fleet;

use App\Models\Vehicle;
use App\Models\MaintenancePrediction;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MaintenancePredictor
{
    /**
     * Get vehicles requiring urgent maintenance.
     */
    public function getUrgentMaintenance(Company $company): array
    {
        return Cache::remember("urgent_maint_{$company->id}", 900, function () use ($company) {
            return Vehicle::where(function ($query) {
                $query->whereRaw('current_odometer >= (last_service_odometer + 10000)') // Standard 10k threshold
                    ->orWhere('status', 'under_maintenance');
            })
                ->get()
                ->toArray();
        });
    }

    /**
     * Predict maintenance dates based on average daily KM.
     */
    public function getMaintenanceForecast(Company $company): array
    {
        return Cache::remember("maint_forecast_{$company->id}", 3600, function () use ($company) {
            return MaintenancePrediction::where('prediction_date', '>=', Carbon::now())
                ->orderBy('prediction_date', 'asc')
                ->take(10)
                ->get()
                ->toArray();
        });
    }

    /**
     * Calculate maintenance cost trends.
     */
    public function getMaintenanceCosts(Company $company): float
    {
        return DB::connection('tenant')->table('bus_profitability_metrics')
            ->whereIn('vehicle_id', function ($q) {
                $q->select('id')->from('vehicles');
            })
            ->sum('maintenance_cost');
    }
}
