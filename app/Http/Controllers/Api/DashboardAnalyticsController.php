<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SaaS\SaaSAnalyticsService;
use App\Services\Fleet\FleetIntelligenceService;
use App\Services\SaaS\RevenueService;
use App\Services\Fleet\UtilizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAnalyticsController extends Controller
{
    protected SaaSAnalyticsService $saasAnalytics;
    protected FleetIntelligenceService $fleetIntelligence;
    protected RevenueService $revenueService;
    protected UtilizationService $utilizationService;

    public function __construct(
        SaaSAnalyticsService $saasAnalytics,
        FleetIntelligenceService $fleetIntelligence,
        RevenueService $revenueService,
        UtilizationService $utilizationService
    ) {
        $this->saasAnalytics = $saasAnalytics;
        $this->fleetIntelligence = $fleetIntelligence;
        $this->revenueService = $revenueService;
        $this->utilizationService = $utilizationService;
    }

    /**
     * Get SaaS revenue trends for Super Admin.
     */
    public function revenueTrends(): JsonResponse
    {
        return response()->json([
            'trends' => $this->revenueService->getRevenueTrends(),
            'forecast' => $this->revenueService->get30DayForecast()
        ]);
    }

    /**
     * Get Fleet utilization heatmap for Company Owners.
     */
    public function utilizationHeatmap(Request $request): JsonResponse
    {
        $company = Auth::user()->company;
        if (!$company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'rate' => $this->utilizationService->getUtilizationRate($company),
            'branch_util' => $this->utilizationService->getBranchUtilization($company)
        ]);
    }

    /**
     * Get unified KPIs for the dashboard.
     */
    public function getKpis(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return response()->json($this->saasAnalytics->getDashboardKPIs());
        }

        if ($user->company) {
            return response()->json($this->fleetIntelligence->getDashboardKPIs($user->company));
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    /**
     * Get Fleet fuel consumption trends for Company Owners.
     */
    public function fuelTrends(): JsonResponse
    {
        $company = Auth::user()->company;
        if (!$company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'trends' => $this->fleetIntelligence->getFuelConsumptionTrends($company)
        ]);
    }
}
