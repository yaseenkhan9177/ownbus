<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresActiveSubscription
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow Super Admins to bypass (if they don't have a company)
        if (!$request->user() || !$request->user()->company_id) {
            return $next($request);
        }

        $company = $request->user()->company;

        // Check subscription access
        if (!$this->subscriptionService->checkAccess($company)) {
            // Redirect to subscription expired page
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Subscription expired or inactive',
                    'message' => 'Your subscription has expired. Please renew to continue using the platform.',
                ], 403);
            }

            return redirect()->route('subscription.expired')
                ->with('error', 'Your subscription has expired. Please renew to continue.');
        }

        return $next($request);
    }
}
