<?php

namespace App\Http\Controllers;

use App\Services\StripeWebhookHandler;
use App\Models\SubscriptionEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected StripeWebhookHandler $webhookHandler;

    public function __construct(StripeWebhookHandler $webhookHandler)
    {
        $this->webhookHandler = $webhookHandler;
    }

    /**
     * Handle incoming Stripe webhooks.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Verify webhook signature
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Check idempotency (prevent duplicate processing)
        if (SubscriptionEvent::isProcessed($event->id)) {
            Log::info('Stripe webhook: Event already processed', ['event_id' => $event->id]);
            return response()->json(['status' => 'already processed'], 200);
        }

        // Handle the event
        try {
            $this->processEvent($event);
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Processing error', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Process the Stripe event.
     * 
     * @param \Stripe\Event $event
     * @return void
     */
    protected function processEvent($event): void
    {
        Log::info('Stripe webhook: Processing event', [
            'event_id' => $event->id,
            'event_type' => $event->type,
        ]);

        match ($event->type) {
            'invoice.payment_succeeded' => $this->webhookHandler->handlePaymentSucceeded($event->toArray()),
            'invoice.payment_failed' => $this->webhookHandler->handlePaymentFailed($event->toArray()),
            'customer.subscription.updated' => $this->webhookHandler->handleSubscriptionUpdated($event->toArray()),
            'customer.subscription.deleted' => $this->webhookHandler->handleSubscriptionDeleted($event->toArray()),
            default => Log::info('Stripe webhook: Unhandled event type', ['type' => $event->type]),
        };
    }
}
