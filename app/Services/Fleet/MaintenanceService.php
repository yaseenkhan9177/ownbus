<?php

namespace App\Services\Fleet;

use App\Models\Vehicle;
use App\Models\VehicleUnavailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceItem;
use App\Models\VehicleServiceInterval;
use App\Models\FinancialTransaction;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class MaintenanceService
{
    /**
     * Predict next service date based on utilization.
     */
    public function predictNextServiceDate(Vehicle $vehicle)
    {
        $remainingKm = $vehicle->next_service_odometer - $vehicle->current_odometer;
        if ($remainingKm <= 0) {
            return now(); // Overdue
        }

        // Calculate average daily mileage from last 30 days of rentals
        // This is an estimation. 
        // We can look at rentals in the last 60 days.
        $avgDailyKm = $this->calculateAverageDailyKm($vehicle, 60);

        if ($avgDailyKm <= 0) {
            return null; // Cannot predict
        }

        $daysUntilService = ceil($remainingKm / $avgDailyKm);
        return now()->addDays($daysUntilService);
    }

    /**
     * Calculate average daily KM based on recent completed rentals.
     */
    protected function calculateAverageDailyKm(Vehicle $vehicle, int $days)
    {
        // Simple heuristic: Total KM from rentals ending in last X days / X days?
        // Or Total KM / Actual rental days?
        // Let's use Total KM driven in period / Period length (utilization based)
        // Or better: Just use Odometers.
        // If we tracked odometer history, we could do (Current - Odo 30 days ago) / 30.
        // Since we don't have explicit odo history logs in this context, we infer from rentals?
        // Rental has `start_odometer` and `end_odometer`.

        $rentals = $vehicle->rentals()
            ->where('status', 'completed')
            ->where('end_date', '>=', now()->subDays($days))
            ->whereNotNull('odometer_end')
            ->whereNotNull('odometer_start')
            ->get();

        if ($rentals->isEmpty()) {
            return 0; // standard fallback, e.g. 50km?
        }

        $totalKm = $rentals->sum(function ($rental) {
            return max(0, $rental->odometer_end - $rental->odometer_start);
        });

        // Averaged over the strict time period (e.g. 60 days) regardless of utilization
        // This gives "Real world usage rate" including idle time.
        return $totalKm / $days;
    }

    /**
     * Sync vehicle status based on active maintenance blocks.
     * Can be called by a heartbeat job or manually.
     */
    public function syncVehicleStatus(Vehicle $vehicle)
    {
        $now = now();
        $hasActiveMaintenance = $vehicle->unavailabilities()
            ->where('reason_type', 'maintenance')
            ->where('start_datetime', '<=', $now)
            ->where('end_datetime', '>', $now)
            ->exists();

        if ($hasActiveMaintenance) {
            if ($vehicle->status !== 'maintenance') {
                $vehicle->update(['status' => 'maintenance']);
            }
        } else {
            // If it was in maintenance, release it to active
            if ($vehicle->status === 'maintenance') {
                $vehicle->update(['status' => 'active']);
            }
        }
    }

    /**
     * Schedule a maintenance block.
     */
    public function scheduleMaintenance(Vehicle $vehicle, array $data, ?User $user = null)
    {
        return DB::transaction(function () use ($vehicle, $data, $user) {
            $unavailability = VehicleUnavailability::create([
                'vehicle_id' => $vehicle->id,
                'start_datetime' => $data['start_datetime'],
                'end_datetime' => $data['end_datetime'],
                'reason_type' => $data['reason_type'] ?? 'maintenance',
                'description' => $data['description'] ?? null,
                'created_by' => $user?->id ?? auth()->id(),
            ]);

            // Immediately sync status
            $this->syncVehicleStatus($vehicle);

            return $unavailability;
        });
    }
    /**
     * Create a new maintenance record with its items.
     */
    public function createRecord(array $data, array $items = [])
    {
        return DB::transaction(function () use ($data, $items) {
            // Generate Maintenance Number
            $nextId = (MaintenanceRecord::max('id') ?? 0) + 1;
            $data['maintenance_number'] = 'MNT-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            $record = MaintenanceRecord::create($data);

            $totalCost = 0;

            foreach ($items as $itemData) {
                $quantity = $itemData['quantity'] ?? 1;
                $unitCost = $itemData['unit_cost'] ?? 0;
                $itemTotal = $quantity * $unitCost;
                $totalCost += $itemTotal;

                $record->items()->create([
                    'item_type' => $itemData['item_type'],
                    'description' => $itemData['description'],
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $itemTotal,
                ]);
            }

            if ($totalCost > 0) {
                $record->update(['total_cost' => $totalCost]);
            }

            return $record;
        });
    }

    /**
     * Update an existing maintenance record.
     */
    public function updateRecord(MaintenanceRecord $record, array $data, array $items = [])
    {
        return DB::transaction(function () use ($record, $data, $items) {
            $record->update($data);

            if (!empty($items)) {
                $record->items()->delete();
                $totalCost = 0;

                foreach ($items as $itemData) {
                    $quantity = $itemData['quantity'] ?? 1;
                    $unitCost = $itemData['unit_cost'] ?? 0;
                    $itemTotal = $quantity * $unitCost;
                    $totalCost += $itemTotal;

                    $record->items()->create([
                        'item_type' => $itemData['item_type'],
                        'description' => $itemData['description'],
                        'quantity' => $quantity,
                        'unit_cost' => $unitCost,
                        'total_cost' => $itemTotal,
                    ]);
                }

                $record->update(['total_cost' => $totalCost]);
            }

            return $record;
        });
    }

    /**
     * Mark a maintenance record as completed and trigger enterprise logic.
     */
    public function completeRecord(MaintenanceRecord $record, array $completionData)
    {
        return DB::transaction(function () use ($record, $completionData) {
            $record->update(array_merge($completionData, [
                'status' => MaintenanceRecord::STATUS_COMPLETED,
            ]));

            // Generate Accounting Entries
            if ($record->total_cost > 0) {
                $this->generateAccountingEntries($record);
            }

            // Update Vehicle Service Intervals
            $this->updateServiceIntervals($record);

            return $record;
        });
    }

    protected function generateAccountingEntries(MaintenanceRecord $record)
    {
        // 1. Create Journal Entry (Header)
        $entry = JournalEntry::create([
            'branch_id'      => $record->vehicle->branch_id ?? null, // Tag to vehicle's branch
            'vehicle_id'     => $record->vehicle_id,               // Auto-tag for cost center analytics
            'date'           => $record->completed_date ?? now(),
            'description'    => "Maintenance Cost: {$record->maintenance_number} — Vehicle: {$record->vehicle->vehicle_number}",
            'reference_type' => MaintenanceRecord::class,
            'reference_id'   => $record->id,
            'is_posted'      => true,
            'posted_at'      => now(),
            'created_by'     => Auth::id(),
        ]);

        // 2. Fetch Accounts
        // Dr: Maintenance Expense (5012)
        // Cr: Accounts Payable (2011) if vendor exists, else Cash (1011)
        $expenseAccount = Account::where('account_code', '5012')->first();
        $creditAccountCode = $record->vendor_id ? '2011' : '1011';
        $creditAccount = Account::where('account_code', $creditAccountCode)->first();

        if ($expenseAccount && $creditAccount) {
            // Debit Maintenance Expense
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $expenseAccount->id,
                'debit'            => (float) $record->total_cost,
                'credit'           => 0,
            ]);

            // Credit Cash/AP
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $creditAccount->id,
                'debit'            => 0,
                'credit'           => (float) $record->total_cost,
            ]);
        }
    }

    protected function updateServiceIntervals(MaintenanceRecord $record)
    {
        // Fetch intervals for this vehicle
        $intervals = VehicleServiceInterval::where('vehicle_id', $record->vehicle_id)->get();

        $completedDate = $record->completed_date ?? now();
        $odometer = $record->odometer_reading;

        foreach ($intervals as $interval) {
            $nextDueOdo = $interval->interval_km && $odometer ? ($odometer + $interval->interval_km) : null;
            $nextDueDate = $interval->interval_days ? Carbon::parse($completedDate)->addDays($interval->interval_days) : null;

            $interval->update([
                'last_service_odometer' => $odometer,
                'last_service_date' => $completedDate,
                'next_due_odometer' => $nextDueOdo,
                'next_due_date' => $nextDueDate,
            ]);
        }
    }
}
