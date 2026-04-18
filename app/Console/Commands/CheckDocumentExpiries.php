<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\NotificationService;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckDocumentExpiries extends Command
{
    protected $signature = 'app:check-document-expiries';
    protected $description = 'Scan fleet vehicles and drivers for document expiries and issue alerts';

    public function handle(TenantService $tenantService, NotificationService $notificationService)
    {
        $this->info('Starting System Fleet Document Expiry Scan...');
        $companies = Company::where('status', 'active')->get();
        $thresholdDate = Carbon::now()->addDays(30);
        $totalAlerts = 0;

        foreach ($companies as $company) {
            $this->info("Scanning Company: {$company->name}");
            
            try {
                $tenantService->switchDatabase($company->database_name);
                Log::info("Scanning expiries for tenant ID: {$company->id}");

                // 1. Vehicle Registration Expiry
                $vehiclesRegistration = Vehicle::whereBetween('registration_expiry', [Carbon::now(), $thresholdDate])
                    ->orWhere('registration_expiry', '<', Carbon::now())
                    ->get();

                foreach ($vehiclesRegistration as $vehicle) {
                    $daysLeft = Carbon::now()->diffInDays(Carbon::parse($vehicle->registration_expiry), false);
                    $notificationService->sendExpiryNotification($vehicle, 'Vehicle Registration', $daysLeft);
                    $totalAlerts++;
                }

                // 2. Vehicle Insurance Expiry
                $vehiclesInsurance = Vehicle::whereBetween('insurance_expiry', [Carbon::now(), $thresholdDate])
                    ->orWhere('insurance_expiry', '<', Carbon::now())
                    ->get();
                    
                foreach ($vehiclesInsurance as $vehicle) {
                    $daysLeft = Carbon::now()->diffInDays(Carbon::parse($vehicle->insurance_expiry), false);
                    $notificationService->sendExpiryNotification($vehicle, 'Vehicle Insurance', $daysLeft);
                    $totalAlerts++;
                }

                // 3. Driver Licenses Expiry
                $driversLicense = Driver::whereBetween('license_expiry_date', [Carbon::now(), $thresholdDate])
                    ->orWhere('license_expiry_date', '<', Carbon::now())
                    ->get();

                foreach ($driversLicense as $driver) {
                    $daysLeft = Carbon::now()->diffInDays(Carbon::parse($driver->license_expiry_date), false);
                    $notificationService->sendExpiryNotification($driver, 'Driving License', $daysLeft);
                    $totalAlerts++;
                }

                // 4. Driver Visa/Emirates ID Expiry
                $driversVisa = Driver::whereBetween('visa_expiry', [Carbon::now(), $thresholdDate])
                    ->orWhere('visa_expiry', '<', Carbon::now())
                    ->get();

                foreach ($driversVisa as $driver) {
                    $daysLeft = Carbon::now()->diffInDays(Carbon::parse($driver->visa_expiry), false);
                    $notificationService->sendExpiryNotification($driver, 'Visa / EID', $daysLeft);
                    $totalAlerts++;
                }

            } catch (\Exception $e) {
                $this->error("Failed to scan company {$company->name}: " . $e->getMessage());
                Log::error("Document expiry scan failed for tenant {$company->id}: " . $e->getMessage());
            }
        }

        $this->info("Scan Complete. Triggered {$totalAlerts} notifications.");
    }
}
