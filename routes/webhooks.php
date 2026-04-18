<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;

/**
 * Stripe Webhook Routes
 * 
 * These routes are exempt from CSRF protection and authentication.
 * Signature verification is handled by the controller.
 */

Route::post('/stripe', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');
