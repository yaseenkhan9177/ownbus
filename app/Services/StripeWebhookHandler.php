<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StripeWebhookHandler
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Handle invoice.payment_succeeded event.
     * 
     * @param array $event
     * @return void
     */
    public function handlePaymentSucceeded(array $event): void
    {
        $invoice = $event['data']['object'];
        $stripeSubscriptionId = $invoice['subscription'] ?? null;

        if (!$stripeSubscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            Log::warning("Subscription not found for Stripe ID: {$stripeSubscriptionId}");
            return;
        }

        // Mark invoice as paid
        $invoiceRecord = SubscriptionInvoice::where('stripe_invoice_id', $invoice['id'])->first();
        if ($invoiceRecord) {
            $invoiceRecord->markAsPaid();
        }

        // Update subscription status
        $subscription->update([
            'status' => 'active',
            'current_period_start' => Carbon::createFromTimestamp($invoice['period_start']),
            'current_period_end' => Carbon::createFromTimestamp($invoice['period_end']),
            'grace_ends_at' => null, // Clear grace period
        ]);

        $this->logEvent($subscription, 'renewed', $event);
    }

    /**
     * Handle invoice.payment_failed event.
     * 
     * @param array $event
     * @return void
     */
    public function handlePaymentFailed(array $event): void
    {
        $invoice = $event['data']['object'];
        $stripeSubscriptionId = $invoice['subscription'] ?? null;

        if (!$stripeSubscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            return;
        }

        // Update invoice attempt count
        $invoiceRecord = SubscriptionInvoice::where('stripe_invoice_id', $invoice['id'])->first();
        if ($invoiceRecord) {
            $invoiceRecord->incrementAttempt();
            $invoiceRecord->update(['status' => 'failed']);
        }

        // Transition to past_due
        $subscription->update(['status' => 'past_due']);

        // Trigger grace period
        $this->subscriptionService->handleGracePeriod($subscription);

        $this->logEvent($subscription, 'payment_failed', $event);
    }

    /**
     * Handle customer.subscription.updated event.
     * 
     * @param array $event
     * @return void
     */
    public function handleSubscriptionUpdated(array $event): void
    {
        $stripeSubscription = $event['data']['object'];
        $stripeSubscriptionId = $stripeSubscription['id'];

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            return;
        }

        // Sync status from Stripe
        $status = $this->mapStripeStatus($stripeSubscription['status']);

        $subscription->update([
            'status' => $status,
            'current_period_start' => Carbon::createFromTimestamp($stripeSubscription['current_period_start']),
            'current_period_end' => Carbon::createFromTimestamp($stripeSubscription['current_period_end']),
        ]);

        $this->logEvent($subscription, 'subscription_updated', $event);
    }

    /**
     * Handle customer.subscription.deleted event.
     * 
     * @param array $event
     * @return void
     */
    public function handleSubscriptionDeleted(array $event): void
    {
        $stripeSubscription = $event['data']['object'];
        $stripeSubscriptionId = $stripeSubscription['id'];

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update(['status' => 'canceled']);

        $this->logEvent($subscription, 'canceled', $event);
    }

    /**
     * Map Stripe subscription status to our internal status.
     * 
     * @param string $stripeStatus
     * @return string
     */
    protected function mapStripeStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'trialing' => 'trialing',
            'active' => 'active',
            'past_due' => 'past_due',
            'canceled' => 'canceled',
            'unpaid' => 'suspended',
            'incomplete' => 'incomplete',
            'incomplete_expired' => 'suspended',
            default => 'suspended',
        };
    }

    /**
     * Log a subscription event with Stripe payload.
     * 
     * @param Subscription $subscription
     * @param string $eventType
     * @param array $stripeEvent
     * @return void
     */
    protected function logEvent(Subscription $subscription, string $eventType, array $stripeEvent): void
    {
        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type' => $eventType,
            'payload_json' => $stripeEvent['data']['object'] ?? [],
            'stripe_event_id' => $stripeEvent['id'] ?? null,
        ]);
    }

    /**
     * Create or update invoice from Stripe data.
     * 
     * @param array $invoiceData
     * @param Subscription $subscription
     * @return void
     */
    public function syncInvoice(array $invoiceData, Subscription $subscription): void
    {
        SubscriptionInvoice::updateOrCreate(
            ['stripe_invoice_id' => $invoiceData['id']],
            [
                'subscription_id' => $subscription->id,
                'company_id' => $subscription->company_id,
                'amount' => $invoiceData['amount_due'] / 100, // Stripe amounts are in cents
                'currency' => strtoupper($invoiceData['currency']),
                'status' => $invoiceData['paid'] ? 'paid' : 'pending',
                'due_date' => $invoiceData['due_date'] ? Carbon::createFromTimestamp($invoiceData['due_date']) : null,
                'paid_at' => $invoiceData['status_transitions']['paid_at']
                    ? Carbon::createFromTimestamp($invoiceData['status_transitions']['paid_at'])
                    : null,
            ]
        );
    }
}
