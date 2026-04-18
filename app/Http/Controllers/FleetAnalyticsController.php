<?php

namespace App\Http\Controllers;

use App\Services\FleetAnalyticsService;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FleetAnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(FleetAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Filters
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();
        $branchId = $request->input('branch_id');

        // Permission Check
        if (!$user->can('view_fleet_analytics') && !$user->hasRole('admin')) {
            abort(403);
        }

        // Branch Scoping
        if (!$user->hasRole('admin') && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        // Security check
        if ($branchId) {
            $branch = Branch::find($branchId);
            if (!$branch) abort(403);
        }

        // 1. Utilization Rate
        $utilizationRate = $this->analyticsService->getUtilizationRate($startDate, $endDate, $branchId);

        // 2. Financial Efficiency per KM
        $revenuePerKm = $this->analyticsService->getRevenuePerKm($startDate, $endDate, $branchId);
        $maintenancePerKm = $this->analyticsService->getMaintenanceCostPerKm($startDate, $endDate, $branchId);

        // 3. Idle Vehicles
        $idleVehicles = $this->analyticsService->getIdleVehicles(7, $branchId);

        // 4. Top Drivers
        $monthYear = $endDate->format('Y-m'); // Use end date month for ranking
        $topDrivers = $this->analyticsService->getTopDrivers(5, $monthYear);

        $branches = Branch::all();

        return view('fleet.analytics', compact(
            'utilizationRate',
            'revenuePerKm',
            'maintenancePerKm',
            'idleVehicles',
            'topDrivers',
            'startDate',
            'endDate',
            'branches',
            'branchId'
        ));
    }
}
