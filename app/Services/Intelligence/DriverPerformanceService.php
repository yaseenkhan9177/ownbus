<?php

namespace App\Services\Intelligence;

use App\Models\Driver;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverPerformanceService
{
    /**
     * Get detailed performance stats for a driver.
     */
    public function getDriverStats(Driver $driver, Carbon $start, Carbon $end): array
    {
        $revenue = $this->calculateRevenue($driver, $start, $end);
        $commission = $this->calculateCommission($driver, $start, $end);
        $rentalsCount = $this->getRentalsCount($driver, $start, $end);
        $availability = $this->calculateAvailability($driver, $start, $end);

        return [
            'driver_name' => $driver->first_name . ' ' . $driver->last_name,
            'rentals_completed' => $rentalsCount,
            'total_revenue' => (float)$revenue,
            'total_commission_paid' => (float)$commission,
            'availability_rate' => round($availability * 100, 2),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
        ];
    }

    /**
     * Revenue attributed to the driver from the ledger.
     */
    protected function calculateRevenue(Driver $driver, Carbon $start, Carbon $end): float
    {
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('rentals', function ($join) {
                $join->on('journal_entries.reference_id', '=', 'rentals.id')
                    ->where('journal_entries.reference_type', '=', Rental::class);
            })
            ->where('rentals.driver_id', $driver->id)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->where('journal_entry_lines.credit', '>', 0) // Income is Credit-normal
            ->sum('journal_entry_lines.credit');
    }

    /**
     * Commission paid to the driver derived from Commission Expense accounts.
     * Note: This assumes commission is posted with a reference to the rental.
     */
    protected function calculateCommission(Driver $driver, Carbon $start, Carbon $end): float
    {
        return (float) DB::connection('tenant')->table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('rentals', function ($join) {
                $join->on('journal_entries.reference_id', '=', 'rentals.id')
                    ->where('journal_entries.reference_type', '=', Rental::class);
            })
            ->where('rentals.driver_id', $driver->id)
            ->where('journal_entries.is_posted', true)
            ->whereBetween('journal_entries.date', [$start->toDateString(), $end->toDateString()])
            ->where('journal_entry_lines.debit', '>', 0) // Expense is Debit-normal
            // Ideally we'd filter by a 'Commission Expense' account group or code (e.g., 5015)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('accounts')
                    ->whereColumn('accounts.id', 'journal_entry_lines.account_id')
                    ->where('accounts.account_name', 'like', '%Commission%');
            })
            ->sum('journal_entry_lines.debit');
    }

    /**
     * Count rentals completed by the driver in the period.
     */
    protected function getRentalsCount(Driver $driver, Carbon $start, Carbon $end): int
    {
        return Rental::where('driver_id', $driver->id)
            ->where('status', Rental::STATUS_COMPLETED)
            ->whereBetween('actual_return_date', [$start, $end])
            ->count();
    }

    /**
     * Calculate availability: 1 - (Days assigned / total days in range).
     */
    protected function calculateAvailability(Driver $driver, Carbon $start, Carbon $end): float
    {
        $totalDays = $start->diffInDays($end) + 1;
        if ($totalDays <= 0) return 0;

        // Sum overlapping days with rentals
        // This is a simplified version; real overlaps require segment analysis.
        $assignedDays = Rental::where('driver_id', $driver->id)
            ->where('status', '!=', Rental::STATUS_CANCELLED)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end]);
            })
            ->get()
            ->sum(function ($rental) use ($start, $end) {
                $rentalStart = max($rental->start_date, $start);
                $rentalEnd = min($rental->end_date, $end);
                return $rentalStart->diffInDays($rentalEnd) + 1;
            });

        return max(0, 1 - ($assignedDays / $totalDays));
    }
}
