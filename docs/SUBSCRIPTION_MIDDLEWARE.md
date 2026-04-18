# Subscription Middleware Integration

## Middleware Registered

- `subscription.active` → RequiresActiveSubscription
- `plan:feature` → CheckPlanFeature

## Usage Examples

### Web Routes

```php
// Require active subscription
Route::middleware(['auth', 'subscription.active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('rentals', RentalController::class);
    Route::resource('branches', BranchController::class);
});

// Require specific feature (BI access)
Route::middleware(['auth', 'subscription.active', 'plan:bi'])->group(function () {
    Route::get('/fleet/analytics', [FleetAnalyticsController::class, 'index']);
    Route::get('/reports', [ReportController::class, 'index']);
});

// Require API access
Route::middleware(['auth:sanctum', 'subscription.active', 'plan:api'])->group(function () {
    Route::get('/api/monitor/anomalies', [AnomalyController::class, 'index']);
});
```

### API Routes

```php
// In routes/api.php
Route::middleware(['auth:sanctum', 'subscription.active'])->group(function () {
    Route::get('/rentals/{rental}/recommendations', [BusRecommendationController::class, 'show']);
});

// BI/API features
Route::middleware(['auth:sanctum', 'subscription.active', 'plan:api'])->group(function () {
    Route::get('/monitor/anomalies', [AnomalyController::class, 'index']);
});
```

## Manual Integration Required

**ACTION NEEDED**: Update `routes/web.php` by adding `'subscription.active'` to the existing auth middleware groups:

```php
// Change from:
Route::middleware(['auth'])->group(function () { ...

// To:
Route::middleware(['auth', 'subscription.active'])->group(function () { ...
```

**For BI/API-gated routes**, add the plan check:

```php
Route::middleware(['auth', 'subscription.active', 'plan:bi'])->group(function () {
    // Analytics routes
});
```

## Subscription Management Routes (Add These)

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'show'])->name('subscription.show');
    Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
    Route::get('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
});
```

These routes are exempt from `subscription.active` since users need access to renew expired subscriptions.
