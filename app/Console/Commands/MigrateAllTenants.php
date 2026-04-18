<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\TenantService;
use Illuminate\Console\Command;

class MigrateAllTenants extends Command
{
    protected $signature = 'app:migrate-all-tenants {--path= : Optional specific migration path to run}';
    protected $description = 'Run pending migrations on all tenant databases';

    public function handle(): int
    {
        $companies = Company::whereNotNull('database_name')
            ->where('database_name', '!=', '')
            ->get();

        if ($companies->isEmpty()) {
            $this->warn('No companies with tenant databases found.');
            return self::SUCCESS;
        }

        $this->info("Running migrations on {$companies->count()} tenant database(s)...");

        $success = 0;
        $failed  = 0;

        foreach ($companies as $company) {
            try {
                $this->line("  → [{$company->database_name}] {$company->name}");
                TenantService::switchDatabase($company->database_name);

                $options = [
                    '--database' => 'tenant',
                    '--force'    => true,
                ];

                if ($this->option('path')) {
                    $options['--path'] = $this->option('path');
                } else {
                    $options['--path'] = 'database/migrations/tenant';
                }

                \Illuminate\Support\Facades\Artisan::call('migrate', $options);
                $output = trim(\Illuminate\Support\Facades\Artisan::output());

                if (str_contains($output, 'Nothing to migrate')) {
                    $this->line("     <fg=gray>Nothing to migrate.</>");
                } else {
                    $this->line("     <fg=green>✓ Done.</>");
                }

                $success++;
            } catch (\Exception $e) {
                $this->error("  ✗ Failed for {$company->database_name}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Completed: {$success} succeeded, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
