<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use App\Services\QuotaService;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected QuotaService $quotaService;

    public function __construct(SubscriptionService $subscriptionService, QuotaService $quotaService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->quotaService = $quotaService;
    }

    /**
     * Show current subscription details.
     */
    public function show(Request $request)
    {
        $company = $request->user()->company;
        $subscription = $this->subscriptionService->getActiveSubscription($company);
        $plan = $subscription?->plan;
        $quotaStatus = $this->quotaService->getQuotaStatus($company);
        
        // Flatten for the view
        $quotaStatus = [
            'vehicles_used' => $quotaStatus['vehicles']['current'] ?? 0,
            'vehicle_limit' => $quotaStatus['vehicles']['limit'] ?? 10,
            'users_used' => $quotaStatus['users']['current'] ?? 0,
            'user_limit' => $quotaStatus['users']['limit'] ?? 5,
        ];

        return view('subscription.show', compact('subscription', 'plan', 'quotaStatus'));
    }

    /**
     * Show subscription expired page.
     */
    public function expired(Request $request)
    {
        $company = $request->user()->company;
        $subscription = $company->subscription;
        $plans = SubscriptionPlan::where('is_active', true)
            ->where('slug', '!=', 'legacy')
            ->get();

        return view('subscription.expired', compact('subscription', 'plans'));
    }

    /**
     * Show upgrade/plan selection page.
     */
    public function upgrade(Request $request)
    {
        $company = $request->user()->company;
        $currentPlan = $this->subscriptionService->getCurrentPlan($company);
        $plans = SubscriptionPlan::where('is_active', true)
            ->where('slug', '!=', 'legacy')
            ->get();

        return view('subscription.upgrade', compact('currentPlan', 'plans'));
    }

    /**
     * Redirect to Stripe Checkout.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $company = $request->user()->company;
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Create or get Stripe customer
            $stripeCustomerId = $company->subscription?->stripe_customer_id;

            if (!$stripeCustomerId) {
                $customer = \Stripe\Customer::create([
                    'email' => $request->user()->email,
                    'name' => $company->name,
                    'metadata' => [
                        'company_id' => $company->id,
                    ],
                ]);
                $stripeCustomerId = $customer->id;
            }

            // Determine price based on billing cycle
            $amount = $validated['billing_cycle'] === 'yearly'
                ? $plan->price_yearly
                : $plan->price_monthly;

            // Create Checkout Session
            $session = \Stripe\Checkout\Session::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $plan->name . ' Plan',
                            'description' => 'Subscription to ' . $plan->name,
                        ],
                        'unit_amount' => $amount * 100, // Stripe uses cents
                        'recurring' => [
                            'interval' => $validated['billing_cycle'] === 'yearly' ? 'year' : 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.upgrade'),
                'metadata' => [
                    'company_id' => $company->id,
                    'plan_id' => $plan->id,
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            \Log::error('Stripe Checkout error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to initiate checkout. Please try again.');
        }
    }

    /**
     * Handle successful checkout.
     */
    public function success(Request $request)
    {
        return view('subscription.success')->with('success', 'Subscription activated successfully!');
    }

    /**
     * Get plan comparison data (API).
     */
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->where('slug', '!=', 'legacy')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'price_monthly' => $plan->price_monthly,
                    'price_yearly' => $plan->price_yearly,
                    'features' => $plan->features,
                    'trial_days' => $plan->trial_days,
                ];
            });

        return response()->json($plans);
    }
}
