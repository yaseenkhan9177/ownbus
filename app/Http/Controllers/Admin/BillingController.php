<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SaaS\AdminBillingService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    protected $billingService;

    public function __construct(AdminBillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function index()
    {
        $kpis = $this->billingService->getKpis();
        $revenueTrend = $this->billingService->getRevenueTrend();
        $subscriptionBreakdown = $this->billingService->getSubscriptionBreakdown();
        $recentInvoices = $this->billingService->getRecentInvoices();
        $failedPayments = $this->billingService->getFailedPayments();

        return view('admin.billing.index', compact(
            'kpis',
            'revenueTrend',
            'subscriptionBreakdown',
            'recentInvoices',
            'failedPayments'
        ));
    }
}
