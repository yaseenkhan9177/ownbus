<?php

namespace App\Services\Intelligence;

use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaintenanceIntelligenceService
{
    /**
     * Get maintenance intelligence for a vehicle over a period.
     */
    public function getVehicleMaintenanceStats(Vehicle $vehicle, Carbon $start, Carbon $end): array
    {
        $totalCost = $this->calculateTotalMaintenanceCost($vehicle, $start, $end);
        $kmDriven = $this->calculateKmDriven($vehicle, $start, $end);
        $downtimePercentage = $this->calculateDowntimePercentage($vehicle, $start, $end);

        $costPerKm = $kmDriven > 0 ? $totalCost / $kmDriven : 0;

        return [
            'vehicle_name' => $vehicle->name,
            'total_maintenance_cost' => (float)$totalCost,
            'km_driven_in_period' => (float)$kmDriven,
            'cost_per_km' => round($costPerKm, 2),
            'downtime_percentage' => round($downtimePercentage * 100, 2),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
        ];
    }

    /**
     * Sum maintenance costs from the ledger for this vehicle.
     */
    protected function calculateTotalMaintenanceCost(Vehicle $vehicle, Carbon $start, Carbon $end): float
    {
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('maintenance_records', function ($join) {
                $join->on('journal_entries.reference_id', '=', 'maintenance_records.id')
                    ->where('journal_entries.reference_type', '=', MaintenanceRecord::class);
            })
            ->where('maintenance_records.vehicle_id', $vehicle->id)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->where('journal_entry_lines.debit', '>', 0) // Expense
            ->sum('journal_entry_lines.debit');
    }

    /**
     * Calculate KM driven during the period using odometer readings from rentals.
     */
    protected function calculateKmDriven(Vehicle $vehicle, Carbon $start, Carbon $end): float
    {
        // Simple approach: Odo at end of last rental in period - Odo at start of first rental in period
        $rentals = DB::connection('tenant')->table('rentals')
            ->where('vehicle_id', $vehicle->id)
            ->whereBetween('actual_return_date', [$start, $end])
            ->orderBy('actual_return_date', 'asc')
            ->get();

        if ($rentals->isEmpty()) return 0;

        $startOdo = $rentals->first()->odometer_start;
        $endOdo = $rentals->last()->odometer_end;

        return max(0, $endOdo - $startOdo);
    }

    /**
     * Calculate downtime percentage (days in maintenance / total days).
     */
    protected function calculateDowntimePercentage(Vehicle $vehicle, Carbon $start, Carbon $end): float
    {
        $totalDays = $start->diffInDays($end) + 1;
        if ($totalDays <= 0) return 0;

        $maintenanceDays = MaintenanceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', MaintenanceRecord::STATUS_COMPLETED)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('completed_date', [$start, $end]);
            })
            ->get()
            ->sum(function ($record) use ($start, $end) {
                $mStart = max($record->start_date ?? $record->scheduled_date, $start);
                $mEnd = min($record->completed_date ?? Carbon::now(), $end);
                return $mStart->diffInDays($mEnd) + 1;
            });

        return min(1, $maintenanceDays / $totalDays);
    }
}
