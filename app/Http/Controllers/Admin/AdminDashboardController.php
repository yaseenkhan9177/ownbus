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

        // Billing details
        $subscriptionsDetails = [
            'active' => \App\Models\Subscription::where('status', 'active')->count(),
            'expired' => \App\Models\Subscription::whereIn('status', ['canceled', 'suspended'])->count(),
            'trialing' => \App\Models\Subscription::where('status', 'trialing')->count(),
        ];

        // 4. Feeds Row
        $recentSignups = $this->dashboardService->getRecentSignups();
        $failedPaymentsFeed = $this->dashboardService->getFailedPaymentsFeed();

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
            'subscriptionsDetails'
        ));
    }
}
