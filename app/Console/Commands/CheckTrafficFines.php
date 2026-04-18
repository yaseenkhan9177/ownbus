<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\TrafficFineService;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTrafficFines extends Command
{
    protected $signature = 'app:check-traffic-fines';
    protected $description = 'Scan external API for traffic fines and map to fleet/rentals';

    public function handle(TenantService $tenantService, TrafficFineService $fineService)
    {
        $this->info('Starting Traffic Fines API Scan...');
        $companies = Company::where('status', 'active')->get();
        $totalFines = 0;

        foreach ($companies as $company) {
            $this->info("Scanning Fines for Company: {$company->name}");
            
            try {
                $tenantService->switchDatabase($company->database_name);
                Log::info("Traffic fine check initiated for tenant ID: {$company->id}");

                $newFines = $fineService->pollAndProcessFines();
                $totalFines += $newFines;

                $this->info("Found {$newFines} new fines for {$company->name}.");
            } catch (\Exception $e) {
                $this->error("Failed to scan company {$company->name}: " . $e->getMessage());
                Log::error("Traffic fine check failed for tenant {$company->id}: " . $e->getMessage());
            }
        }

        $this->info("Scan Complete. Processed {$totalFines} new fines across all tenants.");
    }
}
