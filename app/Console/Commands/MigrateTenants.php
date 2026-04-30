<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for all tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companies = Company::whereNotNull('database_name')
            ->where('status', '!=', 'pending')
            ->get();
        
        if ($companies->isEmpty()) {
            $this->info('No tenant databases found to migrate.');
            return;
        }

        $this->info("Starting migrations for " . $companies->count() . " tenants...");

        foreach ($companies as $company) {
            try {
                $this->line("  → Migrating: [{$company->database_name}] {$company->name}");
                
                TenantService::switchDatabase(
                    $company->database_name
                );
                
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
                
                $output = Artisan::output();
                if (str_contains($output, 'Nothing to migrate')) {
                    $this->line("     <fg=gray>Nothing to migrate.</>");
                } else {
                    $this->line("     <fg=green>✓ Success.</>");
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Failed {$company->database_name}: " . $e->getMessage());
            }
        }
        
        $this->info('All tenant migrations complete!');
    }
}
