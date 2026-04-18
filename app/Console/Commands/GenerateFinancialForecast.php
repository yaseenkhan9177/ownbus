<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\ForecastingService;
use Illuminate\Console\Command;

class GenerateFinancialForecast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:forecast {--company_id= : The ID of the company to forecast for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate financial forecasts based on historical data using linear regression.';

    protected $forecastingService;

    public function __construct(ForecastingService $forecastingService)
    {
        parent::__construct();
        $this->forecastingService = $forecastingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company_id');

        $query = Company::query();
        if ($companyId) {
            $query->where('id', $companyId);
        }

        $companies = $query->get();

        foreach ($companies as $company) {
            $this->info("Generating forecast for Company: {$company->name} ({$company->id})");

            $this->info("  - Revenue...");
            $this->forecastingService->generateForecast($company, 'revenue', 6);

            $this->info("  - Expenses...");
            $this->forecastingService->generateForecast($company, 'expense', 6);
        }

        $this->info('Forecasting complete.');
    }
}
