<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PredictiveMaintenanceService;

class RunPredictiveMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fleet:predict-maintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run predictive maintenance analysis for all vehicles.';

    /**
     * Execute the console command.
     */
    public function handle(PredictiveMaintenanceService $service)
    {
        $this->info('Starting Predictive Maintenance Analysis...');

        $service->generatePredictions();

        $this->info('Analysis Completed.');
    }
}
