<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardService
{
    /**
     * Get all primary KPIs for the dashboard top row.
     */
    public function getKpis(): array
    {
        return [
            'total_companies'      => $this->getTotalCompanies(),
            'active_subscriptions' => $this->getActiveSubscriptions(),
            'mrr'                  => $this->getMRR(),
            'arr'                  => $this->getARR(),
            'churn_rate'           => $this->getChurnRate(),
            'ltv'                  => $this->getLTV(),
            'expiring_trials'      => $this->getExpiringTrials(),
            'suspended_companies'  => $this->getSuspendedCompanies(),
            'failed_payments'      => $this->getFailedPaymentsCount(),
            'pending_approvals'    => $this->getPendingApprovalsCount(),
        ];
    }

    protected function getTotalCompanies(): int
    {
        return Cache::remember('admin.dashboard.total_companies', 60, function () {
            return Company::withoutGlobalScopes()->count();
        });
    }

    protected function getActiveSubscriptions(): int
    {
        return Cache::remember('admin.dashboard.active_subscriptions', 60, function () {
            return Subscription::withoutGlobalScopes()
                ->where('status', 'active')
                ->count();
        });
    }

    protected function getMRR(): float
    {
        return Cache::remember('admin.dashboard.new_mrr', 60, function () {
            return Subscription::withoutGlobalScopes()
                ->where('status', 'active')
                ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
                ->sum('subscription_plans.price_monthly');
        });
    }

    protected function getARR(): float
    {
        return $this->getMRR() * 12;
    }

    protected function getChurnRate(): float
    {
        return Cache::remember('admin.dashboard.churn_rate', 60, function () {
            $totalCustomers = Subscription::withoutGlobalScopes()->count();
            if ($totalCustomers == 0) return 0.0;

            $canceledCustomers = Subscription::withoutGlobalScopes()
                ->whereIn('status', ['canceled', 'suspended'])
                ->count();

            return round(($canceledCustomers / $totalCustomers) * 100, 2);
        });
    }

    protected function getLTV(): float
    {
        return Cache::remember('admin.dashboard.ltv', 60, function () {
            $activeSubsCount = $this->getActiveSubscriptions();
            if ($activeSubsCount == 0) return 0.0;

            $averageRevenue = $this->getMRR() / $activeSubsCount;
            $averageLifetimeMonths = 18; // Default estimated lifetime

            return round($averageRevenue * $averageLifetimeMonths, 2);
        });
    }

    protected function getExpiringTrials(): int
    {
        return Cache::remember('admin.dashboard.expiring_trials', 60, function () {
            return Subscription::withoutGlobalScopes()
                ->where('status', 'trialing')
                ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
                ->count();
        });
    }

    protected function getSuspendedCompanies(): int
    {
        return Cache::remember('admin.dashboard.suspended_companies', 60, function () {
            return Company::withoutGlobalScopes()
                ->where('status', 'suspended')
                ->count();
        });
    }

    protected function getFailedPaymentsCount(): int
    {
        return Cache::remember('admin.dashboard.failed_payments_count', 60, function () {
            return SubscriptionInvoice::withoutGlobalScopes()
                ->where('status', 'failed')
                ->count();
        });
    }

    protected function getPendingApprovalsCount(): int
    {
        return Cache::remember('admin.dashboard.pending_approvals_count', 60, function () {
            return \App\Models\SuperAdminRequest::where('status', 'pending')->count();
        });
    }

    /**
     * Get Chart Data
     */
    public function getRevenueTrend(): array
    {
        return Cache::remember('admin.dashboard.revenue_trend', 60, function () {
            $data = SubscriptionInvoice::withoutGlobalScopes()
                ->where('status', 'paid')
                ->where('paid_at', '>=', now()->subMonths(12))
                ->selectRaw('SUM(amount) as total, DATE_FORMAT(paid_at, "%Y-%m") as month')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $labels = [];
            $values = [];

            // Fill missing months with 0
            for ($i = 11; $i >= 0; $i--) {
                $monthStr = now()->subMonths($i)->format('Y-m');
                $labels[] = now()->subMonths($i)->format('M Y');
                $match = $data->firstWhere('month', $monthStr);
                $values[] = $match ? $this->safeSum($match->total) : 0;
            }

            return [
                'labels' => $labels,
                'data' => $values
            ];
        });
    }

    public function getCompanyGrowth(): array
    {
        return Cache::remember('admin.dashboard.company_growth', 60, function () {
            $data = Company::withoutGlobalScopes()
                ->where('created_at', '>=', now()->subMonths(12))
                ->selectRaw('COUNT(id) as total, DATE_FORMAT(created_at, "%Y-%m") as month')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $labels = [];
            $values = [];

            for ($i = 11; $i >= 0; $i--) {
                $monthStr = now()->subMonths($i)->format('Y-m');
                $labels[] = now()->subMonths($i)->format('M Y');
                $match = $data->firstWhere('month', $monthStr);
                $values[] = $match ? $match->total : 0;
            }

            return [
                'labels' => $labels,
                'data' => $values
            ];
        });
    }

    public function getPlanDistribution(): array
    {
        return Cache::remember('admin.dashboard.plan_distribution', 60, function () {
            $subs = Subscription::withoutGlobalScopes()
                ->where('status', 'active')
                ->with('plan')
                ->get()
                ->groupBy('plan_id');

            $labels = [];
            $data = [];

            foreach ($subs as $planId => $subscriptions) {
                // $subscriptions is a collection, taking the first to get the plan name
                $plan = $subscriptions->first()->plan;
                if ($plan) {
                    $labels[] = $plan->name;
                    $data[] = $subscriptions->count();
                }
            }

            return [
                'labels' => $labels,
                'data' => $data
            ];
        });
    }

    /**
     * Get Feeds
     */
    public function getRecentSignups()
    {
        return Company::withoutGlobalScopes()
            ->with('subscription.plan')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getFailedPaymentsFeed()
    {
        return SubscriptionInvoice::withoutGlobalScopes()
            ->with(['company', 'subscription'])
            ->where('status', 'failed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getTopRevenueCompanies()
    {
        return Cache::remember('admin.dashboard.top_revenue_companies', 60, function () {
            return Company::withoutGlobalScopes()
                ->select('companies.*')
                ->join('subscriptions', 'companies.id', '=', 'subscriptions.company_id')
                ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
                ->where('subscriptions.status', 'active')
                ->orderByDesc('subscription_plans.price_monthly')
                ->limit(5)
                ->get();
        });
    }

    public function getPendingApprovals()
    {
        return \App\Models\SuperAdminRequest::where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getSystemHealth(): array
    {
        $cpu = 0;
        $ramUsage = round(memory_get_usage(true) / 1048576, 2) . 'MB';
        $ramTotal = ini_get('memory_limit');
        $diskUsage = '0GB';
        $diskTotal = '0GB';

        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $cpu = isset($load[0]) ? round($load[0] * 10, 2) : 0;
        }

        if (function_exists('disk_free_space') && function_exists('disk_total_space')) {
            $free = @disk_free_space(base_path());
            $total = @disk_total_space(base_path());
            if ($total) {
                $used = $total - $free;
                $diskUsage = round($used / 1073741824, 2) . 'GB';
                $diskTotal = round($total / 1073741824, 2) . 'GB';
            }
        }

        // DB Status
        $dbStatus = 'Healthy';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Error';
        }

        // Queue Status
        $queueStatus = 'Healthy';
        try {
            $queueSize = DB::table('jobs')->count();
            if ($queueSize > 100) $queueStatus = 'Congested';
        } catch (\Exception $e) {
            $queueStatus = 'Unknown';
        }

        return [
            'cpu_usage'    => $cpu,
            'ram_usage'    => $ramUsage,
            'ram_total'    => $ramTotal,
            'disk_usage'   => $diskUsage,
            'disk_total'   => $diskTotal,
            'db_status'    => $dbStatus,
            'queue_status' => $queueStatus,
            'queue_size'   => $queueSize ?? 0,
        ];
    }

    public function getResourceUsage()
    {
        return Cache::remember('admin.dashboard.resource_usage', 60, function () {
            // vehicles & drivers are now in per-tenant DBs — only count users from central DB
            return Company::withoutGlobalScopes()
                ->withCount(['users'])
                ->orderByDesc('users_count')
                ->limit(5)
                ->get();
        });
    }

    /**
     * Utility Methods
     */
    protected function safeSum($value): float
    {
        return (float) ($value ?? 0.0);
    }
}
