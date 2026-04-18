<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Notifications\PaymentReceived;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create Stripe Payment Intent
     */
    public function createIntent(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:rentals,id',
        ]);

        $booking = $request->user()
            ->rentals()
            ->findOrFail($validated['booking_id']);

        if ($booking->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Booking already paid',
            ], 422);
        }

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($booking->final_amount * 100), // Convert to cents
                'currency' => 'aed',
                'metadata' => [
                    'rental_id' => $booking->id,
                    'customer_id' => $request->user()->id,
                ],
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'booking_id' => $booking->id,
                    'amount' => $booking->final_amount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm payment after successful Stripe payment
     */
    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
            'booking_id' => 'required|exists:rentals,id',
        ]);

        try {
            // Verify payment intent
            $paymentIntent = PaymentIntent::retrieve($validated['payment_intent_id']);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not successful',
                ], 422);
            }

            $booking = $request->user()
                ->rentals()
                ->findOrFail($validated['booking_id']);

            // Update booking
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_received_at' => now(),
            ]);

            // Send notification
            $request->user()->notify(new PaymentReceived($booking));

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get invoice data for booking
     */
    public function invoice(Request $request, $rentalId)
    {
        $booking = $request->user()
            ->rentals()
            ->with('vehicle')
            ->findOrFail($rentalId);

        if ($booking->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice only available for paid bookings',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id' => $booking->id,
                'customer_name' => $request->user()->name,
                'customer_email' => $request->user()->email,
                'vehicle' => $booking->vehicle->name,
                'start_date' => $booking->start_datetime->toISOString(),
                'end_date' => $booking->end_datetime->toISOString(),
                'subtotal' => (float) $booking->subtotal,
                'tax' => (float) $booking->tax_amount,
                'discount' => (float) $booking->discount_amount,
                'total' => (float) $booking->final_amount,
                'payment_date' => $booking->payment_received_at->toISOString(),
                'invoice_url' => route('portal.rentals.invoice', $booking->id),
            ],
        ]);
    }
}
