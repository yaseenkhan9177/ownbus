<?php

namespace App\Jobs;

use App\Models\VehicleFine;
use App\Services\FineRecoveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Recover Fine Job (Queued)
 *
 * Dispatched automatically by VehicleFine::booted() after a fine is created
 * with customer_responsible = true.
 *
 * Posts: DR Accounts Receivable / CR Fine Recovery Income
 * Updates: vehicle_fine.status = 'recovered'
 *
 * Safe to re-queue: FineRecoveryService::recoverFine() is idempotent.
 */
class RecoverFineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Retry on transient failures (DB lock, network).
     * Fail permanently after 3 attempts.
     */
    public int $tries = 3;
    public int $backoff = 60; // seconds between retries

    public function __construct(public readonly int $fineId) {}

    public function handle(FineRecoveryService $recovery): void
    {
        $fine = VehicleFine::find($this->fineId);

        if (!$fine) {
            Log::warning("RecoverFineJob: Fine #{$this->fineId} not found. Skipping.");
            return;
        }

        if (!$fine->customer_responsible) {
            Log::info("RecoverFineJob: Fine #{$fine->id} is not customer-responsible. Skipping.");
            return;
        }

        $recovery->recoverFine($fine);
    }

    public function failed(\Throwable $e): void
    {
        Log::error("RecoverFineJob: Permanently failed for fine #{$this->fineId} — {$e->getMessage()}");
    }
}
