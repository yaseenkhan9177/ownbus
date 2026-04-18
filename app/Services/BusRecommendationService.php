<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\Vehicle;
use App\Models\BusProfitabilityMetric;
use App\Models\BusUtilizationLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BusRecommendationService
{
    /**
     * Get ranked bus recommendations for a rental.
     */
    public function recommendBuses(Rental $rental): Collection
    {
        $companyId = $rental->company_id;

        // 1. Find Available Vehicles
        $availableVehicles = Vehicle::where('status', 'available')
            ->whereDoesntHave('rentals', function ($q) use ($rental) {
                $q->where('id', '!=', $rental->id)
                    ->whereIn('status', ['confirmed', 'assigned', 'active', 'dispatched'])
                    ->where(function ($query) use ($rental) {
                        $query->whereBetween('start_date', [$rental->start_date, $rental->end_date])
                            ->orWhereBetween('end_date', [$rental->start_date, $rental->end_date]);
                    });
            })
            ->whereDoesntHave('unavailabilities', function ($q) use ($rental) {
                $q->where(function ($query) use ($rental) {
                    $query->whereBetween('start_date', [$rental->start_date, $rental->end_date])
                        ->orWhereBetween('end_date', [$rental->start_date, $rental->end_date]);
                });
            })
            ->get();

        // 2. Fetch Metrics for Candidates
        $vehicleIds = $availableVehicles->pluck('id')->toArray();
        $profitability = BusProfitabilityMetric::whereIn('vehicle_id', $vehicleIds)
            ->where('month_year', Carbon::now()->format('Y-m'))
            ->get()
            ->keyBy('vehicle_id');

        $utilizationData = BusUtilizationLog::whereIn('bus_id', $vehicleIds)
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->select('bus_id', DB::raw('SUM(hours_used) as total_hours'))
            ->groupBy('bus_id')
            ->get()
            ->keyBy('bus_id');

        // 3. Score Each Candidate
        return $availableVehicles->map(function ($vehicle) use ($profitability, $utilizationData) {
            $scoreDetails = [];

            // A. Utilization Score (40%) - Lower is better (wear balancing)
            $hoursUsed = $utilizationData[$vehicle->id]->total_hours ?? 0;
            $utilScore = max(0, 100 - ($hoursUsed / 240) * 100); // Normalized to 240 hours/month
            $scoreDetails['utilization'] = round($utilScore * 0.4, 1);

            // B. Profitability Score (40%) - Higher is better
            $profit = $profitability[$vehicle->id]->net_profit ?? 0;
            $profScore = min(100, (max(0, $profit) / 5000) * 100); // Normalized to 5k monthly profit
            $scoreDetails['profitability'] = round($profScore * 0.4, 1);

            // C. Maintenance Buffer Score (20%)
            // Assuming 10,000km interval.
            $nextServiceKm = ceil($vehicle->current_odometer / 10000) * 10000;
            $buffer = $nextServiceKm - $vehicle->current_odometer;
            $maintScore = min(100, ($buffer / 1000) * 100); // Bonus if 1000km+ buffer
            $scoreDetails['maintenance'] = round($maintScore * 0.2, 1);

            $totalScore = array_sum($scoreDetails);

            // D. Generate Reason
            $reason = $this->generateReason($totalScore, $scoreDetails);

            return [
                'vehicle' => $vehicle,
                'score' => round($totalScore, 1),
                'breakdown' => $scoreDetails,
                'reason' => $reason
            ];
        })->sortByDesc('score')->values();
    }

    protected function generateReason(float $total, array $details): string
    {
        if ($total > 80) return "Highly recommended: Excellent profitability and low recent wear.";
        if ($details['profitability'] > 20) return "Top earner: Consistent high-margin performance.";
        if ($details['utilization'] > 35) return "Resource balance: Heavily underutilized recently, good for wear rotation.";
        if ($details['maintenance'] < 5) return "Caution: Close to maintenance threshold, but available for short trips.";

        return "Solid all-rounder for this rental type.";
    }
}
