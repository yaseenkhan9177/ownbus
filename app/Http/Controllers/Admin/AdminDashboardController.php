<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    protected AdminDashboardService $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        // 1. KPI Cards Row
        $kpis = $this->dashboardService->getKpis();

        // 2. Charts Row
        $revenueTrend = $this->dashboardService->getRevenueTrend();
        $companyGrowth = $this->dashboardService->getCompanyGrowth();
        $planDistribution = $this->dashboardService->getPlanDistribution();

        // 3. New Features
        $systemHealth = $this->dashboardService->getSystemHealth();
        $resourceUsage = $this->dashboardService->getResourceUsage();
        $systemActivities = \App\Models\SystemActivity::with('tenant')->latest()->limit(20)->get();
        $systemErrors = \App\Models\SystemErrorLog::with('tenant')->latest()->limit(10)->get();

        // Subscription details for overview widget
        $subscriptionsDetails = [
            'active' => \App\Models\Company::where('subscription_status', 'active')->count(),
            'trial' => \App\Models\Company::where('subscription_status', 'trial')->count(),
            'expiring' => \App\Models\Company::where(function($q) {
                $q->where('subscription_status', 'trial')
                  ->whereDate('trial_ends_at', '<=', now()->addDays(3)->toDateString())
                  ->whereDate('trial_ends_at', '>=', now()->toDateString());
            })->orWhereHas('subscription', function($q) {
                $q->where('status', 'active')
                  ->whereDate('current_period_end', '<=', now()->addDays(3)->toDateString())
                  ->whereDate('current_period_end', '>=', now()->toDateString());
            })->count(),
            'expired' => \App\Models\Company::where('subscription_status', 'expired')->count(),
        ];

        // 4. Feeds Row
        $recentSignups = $this->dashboardService->getRecentSignups();
        $failedPaymentsFeed = $this->dashboardService->getFailedPaymentsFeed();

        // 5. UAE Specifics & New Requirements
        $topRevenueCompanies = $this->dashboardService->getTopRevenueCompanies();
        $pendingApprovals = $this->dashboardService->getPendingApprovals();

        return view('admin.dashboard', compact(
            'kpis',
            'revenueTrend',
            'companyGrowth',
            'planDistribution',
            'recentSignups',
            'failedPaymentsFeed',
            'systemHealth',
            'resourceUsage',
            'systemActivities',
            'systemErrors',
            'subscriptionsDetails',
            'topRevenueCompanies',
            'pendingApprovals'
        ));
    }
}
