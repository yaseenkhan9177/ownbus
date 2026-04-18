<?php

namespace App\Services\SaaS;

use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\Company;
use Illuminate\Support\Facades\Cache;

class AdminBillingService
{
    /**
     * Get primary financial and subscription KPIs.
     * Caches for 10 minutes to prevent DB hammering on dashboard load.
     */
    public function getKpis(): array
    {
        return Cache::remember('admin_billing_kpis', 600, function () {
            // Force global context across all tenants
            $invoicesQuery = SubscriptionInvoice::withoutGlobalScopes();
            $subsQuery = Subscription::withoutGlobalScopes();
            $companyQuery = Company::withoutGlobalScopes();

            // 1. MRR (Monthly Recurring Revenue)
            // Assuming active monthly subscriptions represent MRR. 
            // Better approximation: Sum of recent paid invoices.
            $mrr = $invoicesQuery->clone()
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subMonth())
                ->sum('amount');

            // 2. ARR
            $arr = $mrr * 12;

            // 3. Active Subscriptions
            $activeSubs = $subsQuery->clone()->where('status', 'active')->count();

            // 4. Total Companies (for average rev)
            $totalActiveCompanies = $companyQuery->clone()->where('status', 'active')->count();

            // 5. ARPC (Average Revenue Per Company) Monthly
            $arpc = $totalActiveCompanies > 0 ? ($mrr / $totalActiveCompanies) : 0;

            // 6. Churn Rate (approximated for this period: cancelled / total ever created)
            // Real equation: (Lost Customers / Total Customers at Start of Period) * 100
            $cancelledSubs = $subsQuery->clone()->where('status', 'cancelled')->count();
            $totalSubsEver = $subsQuery->clone()->count();
            $churnRate = $totalSubsEver > 0 ? ($cancelledSubs / $totalSubsEver) * 100 : 0;

            // 7. Failed Payments Count
            $failedPaymentsCount = $invoicesQuery->clone()
                ->whereIn('status', ['failed', 'past_due'])
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            return [
                'mrr' => $mrr,
                'arr' => $arr,
                'active_subscriptions' => $activeSubs,
                'arpc' => $arpc,
                'churn_rate' => $churnRate,
                'failed_payments_count' => $failedPaymentsCount,
            ];
        });
    }

    /**
     * Get revenue aggregated by month for the last 12 months.
     */
    public function getRevenueTrend(): array
    {
        return Cache::remember('admin_revenue_trend_12m', 3600, function () {
            $trend = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);

                $revenue = SubscriptionInvoice::withoutGlobalScopes()
                    ->where('status', 'paid')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->sum('amount');

                $trend[] = [
                    'month' => $date->format('M Y'),
                    'revenue' => (float) $revenue
                ];
            }

            return $trend;
        });
    }

    /**
     * Get distribution of subscriptions by their current status.
     */
    public function getSubscriptionBreakdown(): array
    {
        return Cache::remember('admin_sub_breakdown', 600, function () {
            $statuses = ['active', 'trialing', 'past_due', 'cancelled'];
            $breakdown = [];

            foreach ($statuses as $status) {
                $count = Subscription::withoutGlobalScopes()
                    ->where('status', $status)
                    ->count();

                $breakdown[] = [
                    'status' => ucfirst(str_replace('_', ' ', $status)),
                    'count' => $count
                ];
            }

            return $breakdown;
        });
    }

    /**
     * Get the 10 most recently paid invoices.
     */
    public function getRecentInvoices(int $limit = 10)
    {
        return SubscriptionInvoice::withoutGlobalScopes()
            ->with(['subscription.company', 'subscription.plan'])
            ->where('status', 'paid')
            ->latest('paid_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the latest failed or past due invoices needing attention.
     */
    public function getFailedPayments(int $limit = 10)
    {
        return SubscriptionInvoice::withoutGlobalScopes()
            ->with(['subscription.company'])
            ->whereIn('status', ['failed', 'past_due'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
