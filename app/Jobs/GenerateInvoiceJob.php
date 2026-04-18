<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Background job to generate invoices for subscriptions
 */
class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscription;

    /**
     * Create a new job instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check if invoice already exists for this period
            $existingInvoice = SubscriptionInvoice::where('subscription_id', $this->subscription->id)
                ->where('due_date', $this->subscription->current_period_end)
                ->first();

            if ($existingInvoice) {
                Log::info("Invoice already exists for subscription {$this->subscription->id}");
                return;
            }

            // Get plan details
            $plan = $this->subscription->plan;

            // Create invoice
            SubscriptionInvoice::create([
                'subscription_id' => $this->subscription->id,
                'company_id' => $this->subscription->company_id,
                'amount' => $plan->price_monthly,
                'currency' => 'AED',
                'status' => 'pending',
                'due_date' => $this->subscription->current_period_end,
                'attempt_count' => 0,
            ]);

            Log::info("Invoice generated for subscription {$this->subscription->id}");
        } catch (\Exception $e) {
            Log::error("Failed to generate invoice for subscription {$this->subscription->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger job retry
        }
    }
}
