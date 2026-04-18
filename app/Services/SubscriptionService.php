<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionEvent;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Check if a company has active subscription access.
     * 
     * @param Company $company
     * @return bool
     */
    public function checkAccess(Company $company): bool
    {
        $subscription = $this->getActiveSubscription($company);

        if (!$subscription) {
            return false;
        }

        return $subscription->isActive();
    }

    /**
     * Get the current active subscription for a company.
     * 
     * @param Company $company
     * @return Subscription|null
     */
    public function getActiveSubscription(Company $company): ?Subscription
    {
        return $company->subscription()
            ->with('plan')
            ->whereIn('status', ['trialing', 'active', 'grace', 'past_due'])
            ->first();
    }

    /**
     * Get the current plan for a company.
     * 
     * @param Company $company
     * @return SubscriptionPlan|null
     */
    public function getCurrentPlan(Company $company): ?SubscriptionPlan
    {
        $subscription = $this->getActiveSubscription($company);
        return $subscription?->plan;
    }

    /**
     * Check if a company can use a specific feature.
     * 
     * @param Company $company
     * @param string $feature
     * @return bool
     */
    public function canUseFeature(Company $company, string $feature): bool
    {
        $subscription = $this->getActiveSubscription($company);

        if (!$subscription) {
            return false;
        }

        return $subscription->canUseFeature($feature);
    }

    /**
     * Create a trial subscription for a new company.
     * 
     * @param Company $company
     * @param string $planSlug
     * @return Subscription
     */
    public function createTrialSubscription(Company $company, string $planSlug = 'starter'): Subscription
    {
        $plan = SubscriptionPlan::where('slug', $planSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $subscription = Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'plan_version' => $plan->version,
            'status' => 'trialing',
            'trial_ends_at' => Carbon::now()->addDays($plan->trial_days),
            'current_period_start' => Carbon::now(),
            'current_period_end' => Carbon::now()->addDays($plan->trial_days),
        ]);

        // Log the event
        $this->logEvent($subscription, 'created', [
            'plan' => $plan->slug,
            'trial_days' => $plan->trial_days,
        ]);

        return $subscription;
    }

    /**
     * Handle grace period transition when payment fails.
     * 
     * @param Subscription $subscription
     * @return void
     */
    public function handleGracePeriod(Subscription $subscription): void
    {
        if ($subscription->status !== 'past_due') {
            return;
        }

        $plan = $subscription->plan;
        $graceEndsAt = Carbon::now()->addDays($plan->grace_period_days);

        $subscription->update([
            'status' => 'grace',
            'grace_ends_at' => $graceEndsAt,
        ]);

        $this->logEvent($subscription, 'grace_period_started', [
            'grace_ends_at' => $graceEndsAt->toDateTimeString(),
        ]);
    }

    /**
     * Suspend a subscription.
     * 
     * @param Company $company
     * @param string $reason
     * @return void
     */
    public function suspend(Company $company, string $reason): void
    {
        $subscription = $company->subscription;

        if (!$subscription) {
            return;
        }

        $subscription->update(['status' => 'suspended']);

        $this->logEvent($subscription, 'suspended', [
            'reason' => $reason,
        ]);
    }

    /**
     * Reactivate a suspended subscription.
     * 
     * @param Subscription $subscription
     * @return void
     */
    public function reactivate(Subscription $subscription): void
    {
        $subscription->update([
            'status' => 'active',
            'current_period_start' => Carbon::now(),
            'current_period_end' => Carbon::now()->addMonth(),
            'grace_ends_at' => null,
        ]);

        $this->logEvent($subscription, 'reactivated', []);
    }

    /**
     * Cancel a subscription.
     * 
     * @param Subscription $subscription
     * @param bool $immediately
     * @return void
     */
    public function cancel(Subscription $subscription, bool $immediately = false): void
    {
        $subscription->update(['status' => 'canceled']);

        $this->logEvent($subscription, 'canceled', [
            'immediately' => $immediately,
        ]);
    }

    /**
     * Upgrade/Downgrade subscription plan.
     * 
     * @param Subscription $subscription
     * @param int $newPlanId
     * @param bool $immediate
     * @return void
     */
    public function changePlan(Subscription $subscription, int $newPlanId, bool $immediate = true): void
    {
        $oldPlan = $subscription->plan;
        $newPlan = SubscriptionPlan::findOrFail($newPlanId);

        $subscription->update([
            'plan_id' => $newPlan->id,
            'plan_version' => $newPlan->version,
        ]);

        $eventType = $newPlan->price_monthly > $oldPlan->price_monthly ? 'upgraded' : 'downgraded';

        $this->logEvent($subscription, $eventType, [
            'old_plan' => $oldPlan->slug,
            'new_plan' => $newPlan->slug,
            'immediate' => $immediate,
        ]);
    }

    /**
     * Log a subscription event.
     * 
     * @param Subscription $subscription
     * @param string $eventType
     * @param array $payload
     * @return void
     */
    protected function logEvent(Subscription $subscription, string $eventType, array $payload): void
    {
        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type' => $eventType,
            'payload_json' => $payload,
        ]);
    }
}
