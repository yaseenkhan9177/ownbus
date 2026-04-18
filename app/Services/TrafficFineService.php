<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Rental;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TrafficFineService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Poll external government API for fines and process them into the DB.
     * Stubbed to simulate finding 1-2 random fines for demonstration.
     */
    public function pollAndProcessFines(): int
    {
        $newFinesCount = 0;
        
        // For simulation, we randomly decide if a fine occurred today
        // if (rand(1, 100) > 70) {
        //     return 0; // 70% chance no fines found
        // }

        // Get a random active vehicle
        $vehicle = Vehicle::where('status', 'active')->inRandomOrder()->first();
        if (!$vehicle) return 0;

        // Mock API Response
        $mockApiResponse = [
            [
                'plate_number' => $vehicle->plate_number,
                'fine_date' => Carbon::now()->subHours(rand(1, 48))->toDateTimeString(),
                'fine_amount' => rand(200, 1500),
                'location' => ['Sheikh Zayed Road', 'Al Khail Road', 'Mohammed Bin Zayed Road'][rand(0, 2)],
                'violation_type' => ['Speeding > 20km/h', 'Illegal Parking', 'Salik Evasion', 'Red Light Violation'][rand(0, 3)],
                'ticket_number' => 'DXB-' . str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT),
            ]
        ];

        foreach ($mockApiResponse as $fineData) {
            // Check if fine already imported
            if (Fine::where('ticket_number', $fineData['ticket_number'])->exists()) {
                continue;
            }

            // Find matching rental if any (was vehicle rented at fine_date?)
            $rental = Rental::where('vehicle_id', $vehicle->id)
                ->where('start_date', '<=', $fineData['fine_date'])
                ->where(function($q) use ($fineData) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', $fineData['fine_date']);
                })
                ->whereIn('status', ['active', 'completed', 'closed'])
                ->first();

            // Create the Fine record
            $fine = new Fine();
            $fine->branch_id = $vehicle->branch_id ?? 1; // Fallback or retrieve properly
            $fine->vehicle_id = $vehicle->id;
            $fine->rental_id = $rental ? $rental->id : null; // Link to rental if found
            $fine->driver_id = clone ($rental) ? $rental->driver_id : null; // Might be null
            $fine->ticket_number = $fineData['ticket_number'];
            $fine->fine_date = clone ($fineData['fine_date']);
            $fine->fine_amount = clone ($fineData['fine_amount']);
            $fine->admin_fee = clone ($fineData['fine_amount']) * 0.10; // 10% markup standard
            $fine->total_amount = $fine->fine_amount + $fine->admin_fee;
            $fine->location = $fineData['location'];
            $fine->violation_type = $fineData['violation_type'];
            $fine->status = 'pending';
            
            // Auto charge logic if rental active
            if ($rental && in_array($rental->status, ['active', 'confirmed'])) {
                $fine->charged_to_customer = true;
            } else {
                $fine->charged_to_customer = false; // Company bears it or manual charge
            }

            $fine->save();
            $newFinesCount++;

            // Trigger In-App Notification using our new NotificationSystem
            $message = "New Traffic Fine (AED {$fine->fine_amount}) for {$vehicle->plate_number}. Violation: {$fine->violation_type}.";
            if ($fine->charged_to_customer && $rental) {
                $message .= " Auto-linked to Rental #{$rental->rental_number}.";
            }

            $urgency = clone ($fine->fine_amount) >= 1000 ? 'critical' : 'warning';
            
            $this->notificationService->createInAppNotification(
                null, // All admins
                'fine',
                'Traffic Fine Alert',
                $message,
                $fine,
                $urgency
            );
        }

        return $newFinesCount;
    }
}
