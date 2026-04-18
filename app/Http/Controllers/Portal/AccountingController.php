<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Branch;
use App\Models\JournalEntry;
use App\Models\PayrollBatch;
use App\Models\SalarySlip;
use App\Services\Accounting\PayrollService;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    protected FinancialReportService $reportService;
    protected \App\Services\Accounting\AccountingDashboardService $dashboardService;
    protected PayrollService $payrollService;

    public function __construct(
        FinancialReportService $reportService,
        \App\Services\Accounting\AccountingDashboardService $dashboardService,
        PayrollService $payrollService
    ) {
        $this->reportService = $reportService;
        $this->dashboardService = $dashboardService;
        $this->payrollService = $payrollService;
    }

    /**
     * Accounting Hub / Dashboard
     */
    public function index(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_coa');
        $company = $request->user()->company;

        $kpis = $this->dashboardService->getKpis($company->id);
        $revenueTrend = $this->dashboardService->getRevenueTrend($company->id);
        $expenseBreakdown = $this->dashboardService->getExpenseBreakdown($company->id);

        return view('portal.accounting.index', compact('kpis', 'revenueTrend', 'expenseBreakdown'));
    }

    // =========================================================================
    // AUTHORIZATION HELPER
    // =========================================================================

    private function checkPermission(Request $request, string $permission = 'view_accounting_reports'): void
    {
        $user = $request->user();
        $hasAccess = in_array($user->role, ['company_admin', 'admin', 'super_admin'])
            || $user->can($permission);

        if (!$hasAccess) {
            abort(403, "Unauthorized. {$permission} permission required.");
        }
    }

    /**
     * Resolve and validate start/end dates from request.
     * Default: first day of current month → today.
     * Enterprise rule: start_date must not exceed end_date.
     */
    private function resolveDateRange(Request $request): array
    {
        $start = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfMonth();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        if ($start->gt($end)) {
            abort(422, 'start_date must not be after end_date.');
        }

        return [$start, $end];
    }

    // =========================================================================
    // CHART OF ACCOUNTS
    // =========================================================================

    public function coa(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_coa');
        $company  = $request->user()->company;
        $accounts = Account::whereNull('parent_id')
            ->with('children')
            ->orderBy('account_code')
            ->get();

        return view('portal.accounting.coa', compact('accounts'));
    }

    // =========================================================================
    // JOURNAL LEDGER
    // =========================================================================

    public function journals(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_journals');
        $company = $request->user()->company;

        $journals = JournalEntry::with(['lines.account', 'branch'])
            ->orderBy('date', 'desc')
            ->paginate(50);

        // Manually load creators from central database to avoid cross-connection error
        $userIds = $journals->pluck('created_by')->filter()->unique();
        if ($userIds->isNotEmpty()) {
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
            $journals->each(function ($journal) use ($users) {
                if ($journal->created_by && isset($users[$journal->created_by])) {
                    $journal->setRelation('creator', $users[$journal->created_by]);
                }
            });
        }

        return view('portal.accounting.journals', compact('journals'));
    }

    // =========================================================================
    // REPORTS HUB
    // =========================================================================

    public function reports(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        return view('portal.accounting.reports.index');
    }

    // =========================================================================
    // TRIAL BALANCE
    // =========================================================================

    public function trialBalance(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company  = $request->user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();

        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getTrialBalance($company->id, $start, $end, $branchId);

        return view('portal.accounting.reports.trial-balance', compact('report', 'start', 'end', 'branches', 'branchId'));
    }

    // =========================================================================
    // PROFIT & LOSS
    // =========================================================================

    public function profitLoss(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company  = $request->user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();

        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getProfitAndLoss($company->id, $start, $end, $branchId);

        return view('portal.accounting.reports.profit-loss', compact('report', 'start', 'end', 'branches', 'branchId'));
    }

    // =========================================================================
    // BALANCE SHEET
    // =========================================================================

    public function balanceSheet(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company  = $request->user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();

        $asOfDate = $request->filled('date')
            ? Carbon::parse($request->date)->endOfDay()
            : now()->endOfDay();

        $report = $this->reportService->getBalanceSheet($company->id, $asOfDate, $branchId);

        return view('portal.accounting.reports.balance-sheet', compact('report', 'asOfDate', 'branches', 'branchId'));
    }

    // =========================================================================
    // GENERAL LEDGER
    // =========================================================================

    public function generalLedger(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company  = $request->user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;

        $accounts = Account::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $branches = Branch::all();

        $report   = null;
        $account  = null;

        // Only run report if an account is selected
        if ($request->filled('account_id')) {
            $account = Account::findOrFail($request->account_id);

            [$start, $end] = $this->resolveDateRange($request);

            $report = $this->reportService->getGeneralLedger($account, $start, $end, $branchId);
        } else {
            [$start, $end] = $this->resolveDateRange($request);
        }

        return view('portal.accounting.reports.general-ledger', compact(
            'report',
            'account',
            'accounts',
            'branches',
            'branchId',
            'start',
            'end'
        ));
    }

    // =========================================================================
    // CASH FLOW STATEMENT
    // =========================================================================

    public function cashFlow(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company  = $request->user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();

        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getCashFlow($company->id, $start, $end, $branchId);

        return view('portal.accounting.reports.cash-flow', compact('report', 'start', 'end', 'branches', 'branchId'));
    }

    /**
     * Sales Tax Reports (VAT)
     */
    public function taxReports(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company = $request->user()->company;

        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getTaxSummary($company->id, $start, $end);

        return view('portal.accounting.reports.tax-reports', compact('report', 'start', 'end'));
    }

    // =========================================================================
    // PAYROLL MANAGEMENT
    // =========================================================================

    public function payrollIndex(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company = $request->user()->company;

        $batches = PayrollBatch::with(['branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Manually load creators to avoid cross-connection error
        $userIds = $batches->pluck('created_by')->filter()->unique();
        if ($userIds->isNotEmpty()) {
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
            $batches->each(function ($batch) use ($users) {
                if ($batch->created_by && isset($users[$batch->created_by])) {
                    $batch->setRelation('creator', $users[$batch->created_by]);
                }
            });
        }

        return view('portal.accounting.payroll.index', compact('batches'));
    }

    public function payrollCreate(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company = $request->user()->company;
        $branches = Branch::all();

        return view('portal.accounting.payroll.create', compact('branches'));
    }

    public function payrollStore(Request $request)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company = $request->user()->company;

        $request->validate([
            'period_name' => 'required|string|max:50',
            'branch_id'   => 'nullable|exists:tenant.branches,id',
        ]);

        $batch = $this->payrollService->generateDraftBatch(
            $company->id,
            $request->branch_id,
            $request->period_name
        );

        return redirect()->route('company.accounting.payroll.show', $batch->id)
            ->with('success', 'Payroll batch generated successfully.');
    }

    public function payrollShow(Request $request, $id)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company = $request->user()->company;

        $batch = PayrollBatch::with(['slips.items'])
            ->findOrFail($id);

        // Manually load slip users to avoid cross-connection error (slips.user)
        $userIds = $batch->slips->pluck('user_id')->filter()->unique();
        if ($userIds->isNotEmpty()) {
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
            $batch->slips->each(function ($slip) use ($users) {
                if ($slip->user_id && isset($users[$slip->user_id])) {
                    $slip->setRelation('user', $users[$slip->user_id]);
                }
            });
        }

        return view('portal.accounting.payroll.show', compact('batch'));
    }

    public function payrollPost(Request $request, $id)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company = $request->user()->company;

        $batch = PayrollBatch::findOrFail($id);

        try {
            $this->payrollService->postBatch($batch);
            return back()->with('success', 'Payroll batch posted to ledger successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function salarySlip(Request $request, $id)
    {
        $this->checkPermission($request, 'view_accounting_reports');
        $company = $request->user()->company;

        // Find slip but ensure it belongs to the current tenant context
        $slip = SalarySlip::with(['batch', 'items'])->findOrFail($id);

        // Manually load user from central database
        if ($slip->user_id) {
            $user = \App\Models\User::find($slip->user_id);
            if ($user) {
                $slip->setRelation('user', $user);
            }
        }

        return view('portal.accounting.payroll.salary-slip', compact('slip'));
    }
}
