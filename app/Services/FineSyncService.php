<?php

namespace App\Services;

use App\Models\Fine;
use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;

class FineSyncService
{
    protected $fineService;
    protected $notificationService;

    public function __construct(FineService $fineService, NotificationService $notificationService)
    {
        $this->fineService = $fineService;
        $this->notificationService = $notificationService;
    }

    /**
     * Mock: Pull fines from Authority API (e.g., Dubai Police).
     * In production, this would make HTTP requests.
     */
    public function syncFines()
    {
        // 1. Mock Data from 'API'
        $mockFines = [
            [
                'plate_number' => 'DXB-12345',
                'fine_date' => now()->subDays(2)->toDateTimeString(),
                'amount' => 600.00,
                'authority' => 'Dubai Police',
                'reference' => 'REF-' . rand(10000, 99999),
                'description' => 'Speeding',
            ]
        ];

        foreach ($mockFines as $apiFine) {
            // 2. Find internal Vehicle
            // Assuming we match by plate number (stored in vehicle_number or separated)
            // Let's assume 'vehicle_number' holds the plate.
            $vehicle = Vehicle::where('vehicle_number', $apiFine['plate_number'])->first();

            if (!$vehicle) {
                Log::warning("Vehicle not found for fine: {$apiFine['plate_number']}");
                continue;
            }

            // 3. Check if Fine already exists
            if (Fine::where('reference_number', $apiFine['reference'])->exists()) {
                continue;
            }

            // 4. Find Active Rental at that time
            $rental = Rental::where('vehicle_id', $vehicle->id)
                ->where('actual_start_datetime', '<=', $apiFine['fine_date'])
                ->where(function ($q) use ($apiFine) {
                    $q->where('actual_end_datetime', '>=', $apiFine['fine_date'])
                        ->orWhereNull('actual_end_datetime');
                })
                ->first();

            // 5. Create Fine Record
            $fine = $this->fineService->createFine([
                'company_id' => $vehicle->company_id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $rental ? $rental->driver_id : null,
                'rental_id' => $rental ? $rental->id : null,
                'authority' => $apiFine['authority'],
                'reference_number' => $apiFine['reference'],
                'amount' => $apiFine['amount'],
                'fine_datetime' => $apiFine['fine_date'],
                'status' => 'pending'
            ]);

            // 6. Notify
            $this->notificationService->sendFineNotification($fine);
        }
    }

    /**
     * Submit an Appeal for a Fine.
     */
    public function appealFine(Fine $fine, string $reason)
    {
        $fine->update([
            'appeal_status' => 'pending',
            'appeal_reason' => $reason,
            'appeal_date' => now(),
        ]);

        // In real world, send API request to Authority here...

        return $fine;
    }
}
