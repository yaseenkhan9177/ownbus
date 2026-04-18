<?php

namespace App\Services\SaaS;

use App\Models\Rental;
use App\Models\Subscription;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RevenueService
{
    /**
     * Get Monthly Recurring Revenue (MRR).
     */
    public function getMRR(): float
    {
        return CacheService::rememberTagged(['saas:global'], 'saas_mrr', CacheService::TTL_MEDIUM, function () {
            // Sum prices of all active/grace subscriptions
            return Subscription::whereIn('status', ['active', 'grace'])
                ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
                ->sum('subscription_plans.price_monthly');
        });
    }

    /**
     * Get Annual Recurring Revenue (ARR).
     */
    public function getARR(): float
    {
        return $this->getMRR() * 12;
    }

    /**
     * Get revenue trends for the last 6 months.
     */
    public function getRevenueTrends(): array
    {
        return CacheService::rememberTagged(['saas:global'], 'saas_revenue_trends', CacheService::TTL_LONG, function () {
            return Rental::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(final_amount) as total')
            )
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get()
                ->toArray();
        });
    }

    /**
     * 30-Day Revenue Forecast based on 3-month linear growth.
     */
    public function get30DayForecast(): float
    {
        return CacheService::rememberTagged(['saas:global'], 'saas_revenue_forecast_30d', CacheService::TTL_LONG, function () {
            $mrr = $this->getMRR();
            $trends = $this->getRevenueTrends();

            if (count($trends) < 2) return $mrr * 1.05; // Default 5% bump

            $last = end($trends)['total'];
            $prev = prev($trends)['total'];

            $growth = $prev > 0 ? ($last / $prev) : 1.05;
            return $mrr * $growth;
        });
    }
}
