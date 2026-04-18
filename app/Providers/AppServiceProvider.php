<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Company::class => \App\Policies\CompanyPolicy::class,
        \App\Models\Plan::class => \App\Policies\PlanPolicy::class,
        \App\Models\Branch::class => \App\Policies\BranchPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Cache observers for performance
        \App\Models\Vehicle::observe(\App\Observers\VehicleCacheObserver::class);
        \App\Models\Rental::observe(\App\Observers\RentalCacheObserver::class);
        \App\Models\FuelLog::observe(\App\Observers\FuelLogObserver::class);

        // Operational Data Locks & Processing
        \App\Models\Rental::observe(\App\Observers\RentalObserver::class);
        \App\Models\VehicleFine::observe(\App\Observers\VehicleFineObserver::class);
        \App\Models\Expense::observe(\App\Observers\ExpenseObserver::class);
        \App\Models\Contract::observe(\App\Observers\ContractObserver::class);

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\RentalCompleted::class,
            \App\Listeners\UpdateBIMetrics::class,
        );

        \Illuminate\Support\Facades\Event::subscribe(
            \App\Listeners\AuthLogger::class,
        );

        // Register Policies
        foreach ($this->policies as $model => $policy) {
            \Illuminate\Support\Facades\Gate::policy($model, $policy);
        }

        // RBAC Gates
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        // Strict Portal Access Policies
        \Illuminate\Support\Facades\Gate::define('access-admin-panel', function ($user) {
            return $user->role === 'super_admin';
        });

        \Illuminate\Support\Facades\Gate::define('access-company-panel', function ($user) {
            return $user->company_id !== null;
        });


        // Rate Limiting
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('telematics', function (\Illuminate\Http\Request $request) {
            // 60 requests per minute per IMEI (device)
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->input('imei') ?: $request->ip());
        });

        // View Composer for Company Layout (Notifications)
        \Illuminate\Support\Facades\View::composer('layouts.company', function ($view) {
            if (auth()->check() && auth()->user()->company) {
                $notificationService = app(\App\Services\Portal\NotificationService::class);
                $notifications = $notificationService->getNotifications(auth()->user()->company);
                $view->with('systemNotifications', $notifications);
            }
        });
    }
}
