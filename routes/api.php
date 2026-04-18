<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ===========================================
// Customer Mobile App API (v1)
// ===========================================

Route::prefix('v1')->group(function () {
    // 1. High-Speed Telematics Ingestion (Bypass standard middleware for speed)
    Route::post('/telematics/ping', [\App\Http\Controllers\Api\TelematicsIngestionController::class, 'ping'])
        ->middleware('throttle:telematics');

    // GPS Live Engine (Phase 7D) — device-token authentication
    Route::post('/gps/ping', [\App\Http\Controllers\Api\GpsIngestionController::class, 'ping'])
        ->middleware('throttle:telematics')
        ->name('api.gps.ping');

    Route::get('/gps/vehicle/{vehicle}', [\App\Http\Controllers\Api\GpsIngestionController::class, 'latestLocation'])
        ->middleware('auth:sanctum')
        ->name('api.gps.vehicle-location');

    // GPS Fleet Snapshot — all vehicles in one call for dashboard Leaflet (Phase 8C)
    Route::get('/gps/fleet/{companyId}', [\App\Http\Controllers\Api\GpsIngestionController::class, 'fleetSnapshot'])
        ->middleware('auth:sanctum')
        ->name('api.gps.fleet-snapshot');

    // Public routes - No authentication required
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes - Require Sanctum token
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        // Authentication
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);

        // Vehicles
        Route::get('/vehicles', [VehicleController::class, 'index']);
        Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
        Route::post('/vehicles/check-availability', [VehicleController::class, 'checkAvailability']);

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
        Route::post('/bookings/calculate-price', [BookingController::class, 'calculatePrice']);

        // Payments
        Route::post('/payments/intent', [PaymentController::class, 'createIntent']);
        Route::post('/payments/confirm', [PaymentController::class, 'confirm']);
        Route::get('/invoices/{rentalId}', [PaymentController::class, 'invoice']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread', [NotificationController::class, 'unread']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'read']);
        Route::get('/notification-preferences', [NotificationController::class, 'preferences']);
        Route::put('/notification-preferences', [NotificationController::class, 'updatePreferences']);
    });
});

// ===========================================
// Admin/Internal API (Existing)
// ===========================================

Route::middleware(['auth:sanctum', 'subscription.active', 'throttle:60,1'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/fleet/recommendations', [\App\Http\Controllers\Api\FleetRecommendationController::class, 'index']);
    Route::get('/rentals/{rental}/recommendations', [\App\Http\Controllers\Api\BusRecommendationController::class, 'index']);
    Route::get('/finance/forecasts', [\App\Http\Controllers\Api\FinancialForecastController::class, 'index']);

    Route::get('/maintenance/predictions', [\App\Http\Controllers\Api\MaintenancePredictionController::class, 'index']);
    Route::post('/maintenance/predictions/{id}/schedule', [\App\Http\Controllers\Api\MaintenancePredictionController::class, 'schedule']);

    // API-gated routes (require 'has_api' feature)
    Route::middleware(['plan:api'])->group(function () {
        Route::get('/monitor/anomalies', [\App\Http\Controllers\Api\AnomalyController::class, 'index'])
            ->middleware('ability:read-access,write-access');

        Route::post('/monitor/anomalies/{id}/resolve', [\App\Http\Controllers\Api\AnomalyController::class, 'resolve'])
            ->middleware('ability:write-access');
    });

    // Dashboard Analytics
    Route::get('/analytics/kpis', [\App\Http\Controllers\Api\DashboardAnalyticsController::class, 'getKpis']);
    Route::get('/analytics/revenue-trends', [\App\Http\Controllers\Api\DashboardAnalyticsController::class, 'revenueTrends']);
    Route::get('/analytics/utilization-heatmap', [\App\Http\Controllers\Api\DashboardAnalyticsController::class, 'utilizationHeatmap']);
    Route::get('/analytics/fuel-trends', [\App\Http\Controllers\Api\DashboardAnalyticsController::class, 'fuelTrends']);
});
