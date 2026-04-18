<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

/**
 * Subscription Management Routes
 * 
 * These routes are accessible to authenticated users,
 * but exempt from subscription.active middleware to allow renewals.
 */

Route::middleware(['auth'])->prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/', [SubscriptionController::class, 'show'])->name('show');
    Route::get('/expired', [SubscriptionController::class, 'expired'])->name('expired');
    Route::get('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade');
    Route::post('/checkout', [SubscriptionController::class, 'checkout'])->name('checkout');
    Route::get('/success', [SubscriptionController::class, 'success'])->name('success');

    // API Endpoint
    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');
});
