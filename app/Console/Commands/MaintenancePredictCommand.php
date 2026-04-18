<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PredictiveMaintenanceService;

class MaintenancePredictCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:predict';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run predictive maintenance engine for all vehicles';

    /**
     * Execute the console command.
     */
    public function handle(PredictiveMaintenanceService $service)
    {
        $this->info('Starting Predictive Maintenance Engine...');

        $service->generatePredictions();

        $this->info('Predictions generated successfully.');
    }
}
