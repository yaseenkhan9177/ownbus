<?php

namespace App\Services\Fleet;

use App\Models\Company;
use App\Services\CompanyRevenueService;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;

class FleetDashboardService
{
    protected FleetUtilizationService $utilizationService;
    protected FleetOperationsService $operationsService;
    protected FleetAlertService $alertService;
    protected CompanyRevenueService $revenueService;
    protected FleetFinancialService $financialService;

    public function __construct(
        FleetUtilizationService $utilizationService,
        FleetOperationsService $operationsService,
        FleetAlertService $alertService,
        CompanyRevenueService $revenueService,
        FleetFinancialService $financialService
    ) {
        $this->utilizationService = $utilizationService;
        $this->operationsService = $operationsService;
        $this->alertService = $alertService;
        $this->revenueService = $revenueService;
        $this->financialService = $financialService;
    }

    /**
     * Aggregate all dashboard data with structured payload.
     */
    public function getDashboardData(Company $company): array
    {
        // Each section should handle its own caching logic
        return [
            'kpis' => $this->getKPIs($company),
            'operations' => $this->operationsService->getRentalStats($company),
            'utilization' => [
                'rate' => $this->utilizationService->getUtilizationRate($company),
                'idle_count' => $this->utilizationService->getIdleVehiclesCount($company),
                'trend' => $this->utilizationService->getUtilizationTrend($company), // Keep this for initial load, or remove if purely AJAX
            ],
            'financials' => $this->getFinancials($company),
            'alerts' => $this->alertService->getActiveAlerts($company),
        ];
    }

    protected function getKPIs(Company $company): array
    {
        return CacheService::rememberTagged(["kpis"], "kpis:main", CacheService::TTL_SHORT, function () use ($company) {
            $receivables = $this->financialService->getReceivables($company);
            $pendingCount = $receivables['count'] ?? DB::connection('tenant')->table('rentals')->where('payment_status', 'pending')->count();

            return [
                'active_rentals' => $this->operationsService->getRentalStats($company)['in_progress'],
                'revenue_today' => $this->revenueService->getRevenueStats($company)['today'],
                'utilization_rate' => $this->utilizationService->getUtilizationRate($company),
                'idle_vehicles' => $this->utilizationService->getIdleVehiclesCount($company),
                'pending_invoices_count' => $pendingCount,
            ];
        });
    }

    protected function getFinancials(Company $company): array
    {
        // Separate cache for financials as it might update differently (e.g. nightly or on payment)
        return CacheService::rememberTagged(["financials"], "financials:snapshot", CacheService::TTL_SHORT, function () use ($company) {
            $receivables = $this->financialService->getReceivables($company);
            return array_merge(
                $this->financialService->getFinancialSnapshot($company),
                ['receivables' => $receivables]
            );
        });
    }
}
