<?php

namespace App\Services\SaaS;

use App\Models\Company;
use App\Models\User;
use App\Models\Rental;
use App\Models\Subscription;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SaaSAnalyticsService
{
    protected RevenueService $revenueService;
    protected ChurnService $churnService;

    public function __construct(RevenueService $revenueService, ChurnService $churnService)
    {
        $this->revenueService = $revenueService;
        $this->churnService = $churnService;
    }

    /**
     * Get aggregated KPI tiles for Super Admin.
     */
    public function getLifetimeValue(): float
    {
        return CacheService::rememberTagged(['saas:global'], 'saas_ltv', CacheService::TTL_LONG, function () {
            $mrr = $this->revenueService->getMRR();
            $churnRate = $this->churnService->getChurnRate() / 100;
            $activeCount = Company::where('status', 'active')->count();

            if ($churnRate <= 0 || $activeCount <= 0) return 0;

            $arpu = $mrr / $activeCount;
            return $arpu / $churnRate;
        });
    }

    public function getSystemHealthScore(): int
    {
        return CacheService::rememberTagged(['saas:global'], 'saas_health_score', CacheService::TTL_SHORT, function () {
            $health = 100;

            // Deduct for pending requests over 24h
            $oldRequests = Company::where('status', 'pending')
                ->where('created_at', '<', now()->subDay())
                ->count();
            $health -= ($oldRequests * 5);

            // Deduct for failed jobs (mock logic)
            $health -= 2;

            return max(0, min(100, $health));
        });
    }

    public function getDashboardKPIs(): array
    {
        return [
            'total_companies' => Company::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'mrr' => $this->revenueService->getMRR(),
            'churn_rate' => $this->churnService->getChurnRate(),
            'ltv' => $this->getLifetimeValue(),
            'health_score' => $this->getSystemHealthScore(),
            'forecast_30d' => $this->revenueService->get30DayForecast(),
            'total_revenue' => Rental::where('payment_status', 'paid')->sum('final_amount'),
            'pending_requests' => Company::where('status', 'pending')->count(),
        ];
    }

    /**
     * Get system health indicators.
     */
    public function getSystemHealth(): array
    {
        // Simple mock for now, will integrate with actual health checks later
        return [
            'queue_status' => 'healthy',
            'api_latency' => '45ms',
            'failed_jobs' => DB::table('failed_jobs')->count(),
        ];
    }
}
