<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Fleet\FleetDashboardService;
use App\Services\Portal\BranchDashboardService;
use App\Services\Portal\CompanyDashboardService;
use App\Services\Fleet\FleetUtilizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyDashboardController extends Controller
{
    protected CompanyDashboardService $dashboardService;
    protected BranchDashboardService $branchDashboardService;
    protected FleetUtilizationService $utilizationService;

    public function __construct(
        CompanyDashboardService $dashboardService,
        BranchDashboardService $branchDashboardService,
        FleetUtilizationService $utilizationService
    ) {
        $this->dashboardService = $dashboardService;
        $this->branchDashboardService = $branchDashboardService;
        $this->utilizationService = $utilizationService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('portal.vehicles.index')->with('error', 'No company associated with this account.');
        }

        // Logic for Manager vs Owner dashboard
        if ($request->query('view') === 'manager' || ($user->branch_id && $user->role !== 'owner')) {
            $data = $this->branchDashboardService->getDashboardData($user);
            return view('portal.branch_dashboard', compact('data', 'company'));
        }

        $data = $this->dashboardService->getDashboardData($company);

        return view('portal.dashboard', compact('data', 'company'));
    }

    /**
     * AJAX endpoint for utility chart
     */
    public function getUtilizationTrend()
    {
        $company = Auth::user()->company;

        if (!$company) {
            return response()->json(['error' => 'No company'], 403);
        }

        $data = $this->utilizationService->getUtilizationTrend($company);

        return response()->json($data);
    }
}
