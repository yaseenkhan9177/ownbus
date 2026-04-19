<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('webhooks')
                ->group(base_path('routes/webhooks.php'));

            Route::middleware('web')
                ->group(base_path('routes/subscription.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\SwitchTenantDatabase::class,
            \App\Http\Middleware\SetCompanyContext::class,
        ]);

        $middleware->statefulApi();

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            'isSuperAdmin' => \App\Http\Middleware\IsSuperAdmin::class,
            'customer.auth' => \App\Http\Middleware\CustomerAuth::class,
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'subscription.active' => \App\Http\Middleware\RequiresActiveSubscription::class,
            'plan' => \App\Http\Middleware\CheckPlanFeature::class,
            'driver.pin' => \App\Http\Middleware\DriverPinAuth::class,
        ]);

        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\SwitchTenantDatabase::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ── System Error Logging for Super Admin ──
        $exceptions->reportable(function (Throwable $e) {
            // Ignore 404s, 403s, 401s
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() < 500) {
                return;
            }

            try {
                // Do not log if it's a completely unbooted state or DB is down
                if (app()->has('db')) {
                    \App\Models\SystemErrorLog::create([
                        'tenant_id' => request()->session()->get('company_id') ?? (auth()->check() ? auth()->user()->company_id ?? null : null),
                        'url' => request()->fullUrl(),
                        'error_message' => substr($e->getMessage(), 0, 1000) ?: 'Unknown Error',
                        'stack_trace' => substr($e->getTraceAsString(), 0, 5000),
                    ]);
                }
            } catch (\Throwable $loggingException) {
                // Fail silently to prevent infinite reporting loops
            }
        });

        // ── Credit Enforcement: redirect back with user-friendly error ──
        $exceptions->render(function (
            \App\Exceptions\CreditLimitExceededException $e,
            \Illuminate\Http\Request $request
        ) {
            // API consumers get JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'credit_blocked',
                    'message' => $e->getMessage(),
                ], 422);
            }

            // Browser users get redirected back with a flash error
            return redirect()->back()
                ->withInput()
                ->withErrors(['credit' => $e->getMessage()]);
        });
    })->create();
