<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Rental;
use App\Models\MaintenanceRecord;
use App\Models\Fine;
use App\Models\ContractInvoice;

class TimelineService
{
    /**
     * Get unified chronological timeline of vehicle events.
     *
     * @param int $vehicleId
     * @return array
     */
    public function getVehicleTimeline(int $vehicleId): array
    {
        $timeline = collect();

        // 1. Trips / Rentals
        $rentals = Rental::where('vehicle_id', $vehicleId)->get();
        foreach ($rentals as $rental) {
            if ($rental->status === 'completed') {
                $timeline->push([
                    'type' => 'trip',
                    'date' => $rental->updated_at,
                    'label' => 'Trip Completed (Rental #' . substr($rental->uuid, 0, 6) . ')'
                ]);
            } else {
                $timeline->push([
                    'type' => 'trip',
                    'date' => $rental->created_at,
                    'label' => 'Rental Created (Rental #' . substr($rental->uuid, 0, 6) . ')'
                ]);
            }
        }

        // 2. Maintenance
        $maintenance = MaintenanceRecord::where('vehicle_id', $vehicleId)->get();
        foreach ($maintenance as $maint) {
            $timeline->push([
                'type' => 'maintenance',
                'date' => $maint->start_date ?? $maint->created_at,
                'label' => 'Maintenance: ' . ($maint->description ?? 'Service') . ' (' . ucfirst($maint->status) . ')'
            ]);
        }

        // 3. Fines
        $fines = Fine::where('vehicle_id', $vehicleId)->get();
        foreach ($fines as $fine) {
            $timeline->push([
                'type' => 'fine',
                'date' => $fine->fine_datetime ?? $fine->created_at,
                'label' => 'Fine Detected: ' . $fine->authority . ' - AED ' . $fine->amount
            ]);
        }

        // 4. Invoices (via Contracts)
        $invoices = ContractInvoice::whereHas('contract', function ($q) use ($vehicleId) {
            $q->where('vehicle_id', $vehicleId);
        })->get();
        foreach ($invoices as $inv) {
            $timeline->push([
                'type' => 'invoice',
                'date' => $inv->created_at,
                'label' => 'Invoice Generated: ' . $inv->invoice_number . ' - AED ' . $inv->total_amount
            ]);
        }

        // Sort descending by date
        return $timeline->sortByDesc('date')->values()->all();
    }
}
