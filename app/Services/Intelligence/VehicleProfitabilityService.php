<?php

namespace App\Services\Intelligence;

use App\Models\Vehicle;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Rental;
use App\Models\MaintenanceRecord;
use App\Models\FuelLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleProfitabilityService
{
    /**
     * Get detailed profitability report for a vehicle.
     */
    public function getVehicleReport(Vehicle $vehicle, Carbon $start, Carbon $end): array
    {
        $revenue = $this->calculateRevenue($vehicle, $start, $end);
        $expenses = $this->calculateExpenses($vehicle, $start, $end);

        $totalExpenses = array_sum($expenses);
        $profit = $revenue - $totalExpenses;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            'vehicle' => $vehicle->name . " ({$vehicle->vehicle_number})",
            'revenue' => (float)$revenue,
            'expenses' => $expenses,
            'total_expenses' => (float)$totalExpenses,
            'profit' => (float)$profit,
            'margin_percentage' => round($margin, 2),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
        ];
    }

    /**
     * Calculate Revenue strictly from Journal Entries linked to Rentals of this vehicle.
     */
    protected function calculateRevenue(Vehicle $vehicle, Carbon $start, Carbon $end): float
    {
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('rentals', function ($join) {
                $join->on('journal_entries.reference_id', '=', 'rentals.id')
                    ->where('journal_entries.reference_type', '=', Rental::class);
            })
            ->where('rentals.vehicle_id', $vehicle->id)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->where('journal_entry_lines.credit', '>', 0) // Revenue is Credit-normal
            ->sum('journal_entry_lines.credit');
    }

    /**
     * Calculate direct expenses (Maintenance, Fuel) from Journal Entries.
     */
    protected function calculateExpenses(Vehicle $vehicle, Carbon $start, Carbon $end): array
    {
        // 1. Maintenance Expenses
        $maintenance = (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('maintenance_records', function ($join) {
                $join->on('journal_entries.reference_id', '=', 'maintenance_records.id')
                    ->where('journal_entries.reference_type', '=', MaintenanceRecord::class);
            })
            ->where('maintenance_records.vehicle_id', $vehicle->id)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->where('journal_entry_lines.debit', '>', 0) // Expense is Debit-normal
            ->sum('journal_entry_lines.debit');

        // 2. Fuel Expenses
        $fuel = (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('fuel_logs', function ($join) {
                $join->on('journal_entries.reference_id', '=', 'fuel_logs.id')
                    ->where('journal_entries.reference_type', '=', FuelLog::class);
            })
            ->where('fuel_logs.vehicle_id', $vehicle->id)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->where('journal_entry_lines.debit', '>', 0)
            ->sum('journal_entry_lines.debit');

        return [
            'maintenance' => $maintenance,
            'fuel' => $fuel,
        ];
    }
}
