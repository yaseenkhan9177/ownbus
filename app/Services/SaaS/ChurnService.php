<?php

namespace App\Services\SaaS;

use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ChurnService
{
    /**
     * Calculate the Churn Rate for the last 30 days.
     */
    public function getChurnRate(): float
    {
        return Cache::remember('saas_churn_rate', 3600, function () {
            $startOfMonth = Carbon::now()->startOfMonth();

            $totalAtStart = Subscription::where('created_at', '<', $startOfMonth)
                ->whereNotIn('status', ['canceled'])
                ->count();

            if ($totalAtStart === 0) return 0.0;

            $canceledInPeriod = SubscriptionEvent::where('event_type', 'canceled')
                ->where('created_at', '>=', $startOfMonth)
                ->count();

            return round(($canceledInPeriod / $totalAtStart) * 100, 2);
        });
    }

    /**
     * Get subscription health score (Active vs Trialing vs Canceled).
     */
    public function getHealthStats(): array
    {
        return Cache::remember('saas_health_stats', 900, function () {
            return [
                'active' => Subscription::where('status', 'active')->count(),
                'trialing' => Subscription::where('status', 'trialing')->count(),
                'past_due' => Subscription::where('status', 'past_due')->count(),
                'canceled' => Subscription::where('status', 'canceled')->count(),
            ];
        });
    }

    /**
     * Calculate Trial Conversion Percentage.
     */
    public function getTrialConversionRate(): float
    {
        return Cache::remember('saas_trial_conversion', 3600, function () {
            $totalEndedTrials = SubscriptionEvent::where('event_type', 'trial_ended')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();

            if ($totalEndedTrials === 0) return 0.0;

            $converted = SubscriptionEvent::where('event_type', 'upgraded')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();

            return round(($converted / $totalEndedTrials) * 100, 2);
        });
    }
}
