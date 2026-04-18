<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show payment page for a booking
     */
    public function show(Rental $rental): View
    {
        // Ensure customer owns this rental
        if ($rental->customer_id !== auth()->id()) {
            abort(403);
        }

        // Check if already paid
        if ($rental->payment_status === 'paid') {
            return redirect()->route('portal.bookings.show', $rental)
                ->with('info', 'This booking is already paid.');
        }

        // Create Stripe Payment Intent
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $rental->final_amount * 100, // Convert to cents
                'currency' => 'aed',
                'metadata' => [
                    'rental_id' => $rental->id,
                    'customer_id' => auth()->id(),
                ],
                'description' => "Booking #{$rental->rental_number} - {$rental->vehicle->name}",
            ]);

            return view('portal.payments.show', compact('rental', 'paymentIntent'));
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to initialize payment. Please try again.');
        }
    }

    /**
     * Process payment success
     */
    public function success(Request $request, Rental $rental)
    {
        // Ensure customer owns this rental
        if ($rental->customer_id !== auth()->id()) {
            abort(403);
        }

        // Verify payment intent
        $paymentIntentId = $request->query('payment_intent');

        if ($paymentIntentId) {
            try {
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

                if ($paymentIntent->status === 'succeeded') {
                    // Update rental payment status
                    $rental->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed', // Auto-confirm on payment
                        'stripe_payment_intent_id' => $paymentIntentId,
                    ]);

                    // TODO: Send confirmation email
                    // TODO: Send SMS notification

                    $rental->status = 'confirmed';
                    $rental->payment_status = 'paid';
                    $rental->save();

                    // Send payment confirmation notification
                    $rental->customer->notify(new \App\Notifications\PaymentReceived($rental));

                    // TODO: Send confirmation SMS

                    return view('portal.payments.success', compact('rental'));
                }
            } catch (\Exception $e) {
                return redirect()->route('portal.bookings.show', $rental)
                    ->with('error', 'Unable to confirm payment. Please contact support.');
            }
        }

        return redirect()->route('portal.bookings.show', $rental);
    }

    /**
     * Handle payment cancellation
     */
    public function cancel(Rental $rental)
    {
        // Ensure customer owns this rental
        if ($rental->customer_id !== auth()->id()) {
            abort(403);
        }

        return redirect()->route('portal.bookings.show', $rental)
            ->with('info', 'Payment was cancelled. You can try again when ready.');
    }

    /**
     * Webhook handler for Stripe events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $rentalId = $paymentIntent->metadata->rental_id ?? null;

                    if ($rentalId) {
                        $rental = Rental::find($rentalId);
                        if ($rental && $rental->payment_status !== 'paid') {
                            $rental->update([
                                'payment_status' => 'paid',
                                'status' => 'confirmed',
                                'stripe_payment_intent_id' => $paymentIntent->id,
                            ]);

                            // TODO: Send confirmation notifications
                        }
                    }
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $rentalId = $paymentIntent->metadata->rental_id ?? null;

                    if ($rentalId) {
                        $rental = Rental::find($rentalId);
                        if ($rental) {
                            $rental->update(['payment_status' => 'failed']);
                        }
                    }
                    break;

                default:
                    // Unexpected event type
                    return response()->json(['error' => 'Unexpected event type'], 400);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
