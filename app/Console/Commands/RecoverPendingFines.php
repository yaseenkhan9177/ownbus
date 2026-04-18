<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\FineRecoveryService;
use Illuminate\Console\Command;

/**
 * Batch recover all pending unrecovered customer-responsible fines.
 *
 * Use this for:
 * 1. One-time catch-up when first deploying Phase 8B
 * 2. Daily cron safety net for any fines that slipped through the queue
 *
 * Artisan: php artisan fines:recover-pending
 * Schedule: daily at 03:30 (registered in routes/console.php)
 */
class RecoverPendingFines extends Command
{
    protected $signature = 'fines:recover-pending
                            {--dry-run : Preview without posting journal entries}
                            {--company= : Process a specific company ID only}';

    protected $description = 'Batch-recover all unrecovered customer-responsible fines (idempotent safety net)';

    public function handle(FineRecoveryService $recovery): int
    {
        $dryRun    = $this->option('dry-run');
        $companyId = $this->option('company');

        $this->newLine();
        $this->line('  <fg=cyan>Fine Recovery Engine</> — ' . ($dryRun ? '<fg=yellow>DRY RUN</>' : '<fg=green>LIVE</>'));
        $this->newLine();

        $query = \App\Models\VehicleFine::where('customer_responsible', true)
            ->whereNull('journal_entry_id')
            ->whereNotNull('customer_id')
            ->where('status', 'pending')
            ->with(['customer', 'vehicle']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $fines = $query->get();

        if ($fines->isEmpty()) {
            $this->info('  ✅ No pending fines require recovery.');
            return 0;
        }

        if ($dryRun) {
            $this->warn("  DRY RUN — {$fines->count()} fine(s) would be recovered:");
            $this->table(
                ['Fine #', 'Vehicle', 'Customer', 'Amount (AED)', 'Due Date'],
                $fines->map(fn($f) => [
                    $f->fine_number,
                    $f->vehicle?->vehicle_number ?? 'N/A',
                    $f->customer?->name ?? 'N/A',
                    number_format($f->amount, 2),
                    $f->due_date?->format('d M Y') ?? 'N/A',
                ])->toArray()
            );
            return 0;
        }

        $recovered = 0;
        $errors    = 0;

        $fines->each(function ($fine) use ($recovery, &$recovered, &$errors) {
            try {
                $recovery->recoverFine($fine);
                $recovered++;
                $this->line("  ✅ Recovered fine #{$fine->fine_number} — AED " . number_format($fine->amount, 2));
            } catch (\Throwable $e) {
                $errors++;
                $this->warn("  ⚠  Fine #{$fine->fine_number} failed: {$e->getMessage()}");
            }
        });

        $this->newLine();
        $this->info("  Recovered: {$recovered} | Errors: {$errors}");

        return $errors > 0 ? 1 : 0;
    }
}
