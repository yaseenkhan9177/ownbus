<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Handle an incoming request.
     *
     * Feature is passed as middleware parameter: 'plan:bi' or 'plan:api'
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        // Allow Super Admins to bypass
        if (!$request->user() || !$request->user()->company_id) {
            return $next($request);
        }

        $company = $request->user()->company;

        // Strip 'has_' prefix if present (e.g., 'bi' becomes 'has_bi')
        $featureKey = str_starts_with($feature, 'has_') ? $feature : "has_{$feature}";

        // Check feature access
        if (!$this->subscriptionService->canUseFeature($company, $featureKey)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Feature not available',
                    'message' => "Your current plan does not include access to {$feature}. Please upgrade.",
                ], 403);
            }

            return redirect()->route('subscription.upgrade')
                ->with('error', "Your plan does not include {$feature}. Upgrade to unlock this feature.");
        }

        return $next($request);
    }
}
