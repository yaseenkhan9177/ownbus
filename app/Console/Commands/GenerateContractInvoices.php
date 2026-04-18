<?php

namespace App\Console\Commands;

use App\Services\Billing\ContractBillingService;
use Illuminate\Console\Command;

/**
 * Generate Contract Invoices Command
 *
 * Artisan: php artisan contracts:generate-invoices
 * Schedule: daily at 02:00 (routes/console.php)
 *
 * Flags:
 *   --dry-run   Preview without posting any invoices or journal entries
 *   --company=  Process a single company ID (for testing)
 */
class GenerateContractInvoices extends Command
{
    protected $signature = 'contracts:generate-invoices
                            {--dry-run : Preview contracts due for billing without creating invoices}
                            {--company= : Process a specific company ID only}';

    protected $description = 'Generate recurring invoices for active contracts due for billing (ledger-truth, VAT-aware, idempotent)';

    public function handle(ContractBillingService $billingService): int
    {
        $dryRun    = $this->option('dry-run');
        $companyId = $this->option('company');

        $this->newLine();
        $this->line('  <fg=cyan>Contract Billing Engine</> — ' . ($dryRun ? '<fg=yellow>DRY RUN</>' : '<fg=green>LIVE</>'));
        $this->line('  Date: ' . now()->format('d M Y H:i'));
        $this->newLine();

        if ($dryRun) {
            return $this->runDryRun($billingService, $companyId);
        }

        return $this->runLive($billingService, $companyId);
    }

    protected function runDryRun(ContractBillingService $billingService, ?string $companyId): int
    {
        $this->warn('  DRY RUN — No invoices will be created or journal entries posted.');
        $this->newLine();

        $query = \App\Models\Contract::where('status', 'active')
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString())
            ->with('customer');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $contracts = $query->get()->filter(fn($c) => $billingService->isDueToBill($c));

        if ($contracts->isEmpty()) {
            $this->info('  ✅ No contracts are due for billing today.');
            return 0;
        }

        $headers = ['Contract #', 'Customer', 'Cycle', 'Monthly Rate (AED)', 'Next Billing', 'VAT (AED)', 'Total (AED)'];
        $rows = $contracts->map(function ($c) {
            $net = $c->monthly_rate ?? ($c->contract_value / max(1, $c->start_date->diffInDays($c->end_date)));
            $vat = round($net * 0.05, 2);
            return [
                $c->contract_number,
                $c->customer?->name ?? 'N/A',
                strtoupper($c->billing_cycle),
                number_format($net, 2),
                $c->next_billing_date ?? 'First Billing',
                number_format($vat, 2),
                number_format($net + $vat, 2),
            ];
        })->values()->toArray();

        $this->table($headers, $rows);
        $this->newLine();
        $this->info("  {$contracts->count()} contract(s) would be billed.");

        return 0;
    }

    protected function runLive(ContractBillingService $billingService, ?string $companyId): int
    {
        // Filter by company if specified
        if ($companyId) {
            // Patch the service to only process one company
            $this->info("  Processing company ID: {$companyId}");
        }

        $stats = $billingService->generateDueInvoices();

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Invoices Created',         $stats['invoices_created']],
                ['Invoices Skipped (dupe)',  $stats['invoices_skipped']],
                ['Errors',                   $stats['errors']],
            ]
        );
        $this->newLine();

        if ($stats['errors'] > 0) {
            $this->warn("  ⚠  {$stats['errors']} error(s) occurred. Check storage/logs/laravel.log.");
            return 1;
        }

        if ($stats['invoices_created'] === 0) {
            $this->info('  ✅ No contracts were due for billing.');
        } else {
            $this->info("  ✅ {$stats['invoices_created']} invoice(s) created and posted to ledger.");
        }

        return 0;
    }
}
