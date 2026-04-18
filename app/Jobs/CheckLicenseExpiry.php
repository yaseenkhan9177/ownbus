<?php

namespace App\Jobs;

use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckLicenseExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Checking for driver license expiries...');

        $expiringSoon = Driver::where('status', Driver::STATUS_ACTIVE)
            ->where('license_expiry_date', '<=', now()->addDays(30))
            ->where('license_expiry_date', '>', now())
            ->get();

        foreach ($expiringSoon as $driver) {
            // In a real app, we would send a notification to the company admin
            // e.g., $driver->company->notify(new LicenseExpiringNotification($driver));
            Log::warning("Driver license expiring soon: {$driver->name} (Code: {$driver->driver_code}) on {$driver->license_expiry_date->format('Y-m-d')}");
        }

        $expired = Driver::where('status', Driver::STATUS_ACTIVE)
            ->where('license_expiry_date', '<', now())
            ->get();

        foreach ($expired as $driver) {
            // Log or potentially auto-suspend if policy allows, but user didn't ask for auto-suspend.
            // "Block assignment" is handled in RentalRequest.
            Log::error("Driver license EXPIRED: {$driver->name} (Code: {$driver->driver_code}) on {$driver->license_expiry_date->format('Y-m-d')}");
        }
    }
}
