<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\DriverDocument;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDocumentExpiry extends Command
{
    protected $signature = 'documents:check-expiry';
    protected $description = 'Check for expiring vehicle and driver documents and notify admins';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Starting document expiry check...');

        $thresholds = [30, 14, 7, 1]; // Notify at 30, 14, 7, and 1 days before expiry

        foreach ($thresholds as $days) {
            $targetDate = Carbon::now()->addDays($days)->toDateString();

            // 1. Check Vehicles (Registration & Insurance)
            $vehicles = Vehicle::whereDate('registration_expiry', $targetDate)
                ->orWhereDate('insurance_expiry', $targetDate)
                ->get();

            foreach ($vehicles as $vehicle) {
                $type = $vehicle->registration_expiry->toDateString() === $targetDate ? 'Registration' : 'Insurance';
                $this->notificationService->sendExpiryNotification($vehicle, "Vehicle {$type} ({$vehicle->vehicle_number})", $days);
            }

            // 2. Check Driver Documents (License, Visa, etc.)
            $documents = DriverDocument::whereDate('expiry_date', $targetDate)->with('driverProfile.user')->get();

            foreach ($documents as $doc) {
                $driverName = $doc->driverProfile->user->name ?? 'Unknown Driver';
                $this->notificationService->sendExpiryNotification($doc, "Driver {$doc->document_type} ({$driverName})", $days);
            }
        }

        $this->info('Expiry check completed.');
    }
}
