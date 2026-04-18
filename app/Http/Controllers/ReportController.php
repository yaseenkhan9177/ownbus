<?php

namespace App\Http\Controllers;

use App\Services\FinancialReportService;
use App\Models\Company;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\Fleet\ReportService;

class ReportController extends Controller
{
    protected $financialReportService;
    protected $reportService;

    public function __construct(FinancialReportService $financialReportService, ReportService $reportService)
    {
        $this->financialReportService = $financialReportService;
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        if (!$companyId) {
            abort(403, 'User is not associated with any company.');
        }

        // Default Filters
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();
        $branchId = $request->input('branch_id');

        // Permission Check: Allow super_admin, admin role, or specific permission
        // company_admin (Owner) always has access to their own company reports
        $hasPermission = $user->can('view_financial_reports') || $user->hasRole('admin') || $user->isSuperAdmin() || $user->role === 'company_admin';

        if (!$hasPermission) {
            abort(403, 'Unauthorized. Financial report permission required.');
        }

        // Branch Scoping
        if (!$user->hasRole('admin') && !$user->isSuperAdmin()) {
            if ($user->branch_id) {
                $branchId = $user->branch_id;
            }
        }

        // Security: Ensure branch belongs to company if selected
        if ($branchId) {
            $branch = Branch::find($branchId);
            if (!$branch) {
                abort(403, 'Unauthorized branch access');
            }
        }

        // Generate Financial Reports
        $pnl = $this->financialReportService->getProfitAndLoss($companyId, $startDate, $endDate, $branchId);
        $balanceSheet = $this->financialReportService->getBalanceSheet($companyId, $endDate, $branchId);

        // Generate Vehicle Performance Report
        $vehiclePerformance = $this->reportService->generateVehiclePerformanceReport($companyId, $startDate, $endDate);

        $branches = Branch::all();

        return view('reports.index', compact('pnl', 'balanceSheet', 'vehiclePerformance', 'startDate', 'endDate', 'branches', 'branchId'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $type = $request->input('type');

        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();

        if ($type === 'vehicle_performance') {
            $data = $this->reportService->generateVehiclePerformanceReport($companyId, $startDate, $endDate);
            $headers = ['Vehicle Number', 'Make/Model', 'Rental Count', 'Total Revenue', 'Status'];
            return $this->reportService->exportToCsv($data, $headers, 'vehicle_performance.csv');
        }

        return redirect()->back();
    }
}
