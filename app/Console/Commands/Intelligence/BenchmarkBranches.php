<?php

namespace App\Console\Commands\Intelligence;

use Illuminate\Console\Command;

class BenchmarkBranches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'branches:benchmark';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze and rank branch performance weekly across all companies';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Intelligence\BranchBenchmarkService $benchmarkService)
    {
        $companies = \App\Models\Company::all();
        $this->info("Benchmarking branches for {$companies->count()} companies...");

        foreach ($companies as $company) {
            $result = $benchmarkService->analyzeCompany($company);

            foreach ($result['branches'] as $stats) {
                \Illuminate\Support\Facades\DB::connection('tenant')->table('branch_benchmark_snapshots')->insert([
                    'branch_id' => $stats['branch_id'],
                    'score' => $stats['score'],
                    'breakdown_json' => json_encode($stats['breakdown']),
                    'calculated_at' => now(),
                ]);
            }

            $this->info("Company #{$company->name}: Ranked {$company->branches()->count()} branches.");
        }

        $this->info('Weekly branch benchmarking completed.');
    }
}
