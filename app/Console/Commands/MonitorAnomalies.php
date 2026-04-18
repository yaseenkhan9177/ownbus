<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\AnomalyDetectionService;
use Illuminate\Console\Command;

class MonitorAnomalies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:anomalies {--company_id= : The ID of the company to monitor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan for financial and operational anomalies.';

    protected $detectionService;

    public function __construct(AnomalyDetectionService $detectionService)
    {
        parent::__construct();
        $this->detectionService = $detectionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company_id');

        $query = Company::query()->where('status', 'active');
        if ($companyId) {
            $query->where('id', $companyId);
        }

        $companies = $query->get();

        foreach ($companies as $company) {
            $this->info("Scanning Company: {$company->name} ({$company->id})");
            $this->detectionService->detectAnomalies($company);
        }

        $this->info('Anomaly scan complete.');
    }
}
