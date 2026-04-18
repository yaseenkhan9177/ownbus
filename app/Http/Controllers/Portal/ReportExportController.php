<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

// Models
use App\Models\Rental;
use App\Models\Branch;
use App\Models\Account;
use App\Models\VendorBill;

// Services
use App\Services\FinancialReportService;

// Exports
use App\Exports\InvoiceExport;
use App\Exports\BalanceSheetExport;
use App\Exports\ProfitLossExport;
use App\Exports\CashFlowExport;
use App\Exports\GeneralLedgerExport;
use App\Exports\VendorBillExport;

class ReportExportController extends Controller
{
    protected FinancialReportService $reportService;

    public function __construct(FinancialReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    private function resolveDateRange(Request $request): array
    {
        $start = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfMonth();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        return [$start, $end];
    }

    // =========================================================================
    // INVOICES (Rentals)
    // =========================================================================

    private function getInvoicesData()
    {
        return Rental::whereIn('status', ['confirmed', 'assigned', 'dispatched', 'completed', 'closed'])
            ->whereNotNull('start_date')
            ->where('final_amount', '>', 0)
            ->with(['customer', 'transactions'])
            ->orderBy('created_at', 'desc')
            ->get(); // Get all for export
    }

    public function invoicesExcel(Request $request)
    {
        $company = Auth::user()->company;
        $invoices = $this->getInvoicesData();
        return Excel::download(new InvoiceExport($company, $invoices), 'invoices-report-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function invoicesPdf(Request $request)
    {
        $company = Auth::user()->company;
        $invoices = $this->getInvoicesData();

        $pdf = Pdf::loadView('exports.invoices', compact('company', 'invoices'))->setPaper('a4', 'landscape');
        return $pdf->download('invoices-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // BALANCE SHEET
    // =========================================================================

    public function balanceSheetExcel(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        $asOfDate = $request->filled('date') ? Carbon::parse($request->date)->endOfDay() : now()->endOfDay();

        $report = $this->reportService->getBalanceSheet($company->id, $asOfDate, $branchId);

        return Excel::download(new BalanceSheetExport($company, $report, $asOfDate, $branches, $branchId), 'balance-sheet-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function balanceSheetPdf(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        $asOfDate = $request->filled('date') ? Carbon::parse($request->date)->endOfDay() : now()->endOfDay();

        $report = $this->reportService->getBalanceSheet($company->id, $asOfDate, $branchId);

        $pdf = Pdf::loadView('exports.balance-sheet', compact('company', 'report', 'asOfDate', 'branches', 'branchId'));
        return $pdf->download('balance-sheet-' . now()->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // PROFIT & LOSS
    // =========================================================================

    public function profitLossExcel(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getProfitAndLoss($company->id, $start, $end, $branchId);

        return Excel::download(new ProfitLossExport($company, $report, $start, $end, $branches, $branchId), 'profit-loss-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function profitLossPdf(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getProfitAndLoss($company->id, $start, $end, $branchId);

        $pdf = Pdf::loadView('exports.profit-loss', compact('company', 'report', 'start', 'end', 'branches', 'branchId'));
        return $pdf->download('profit-loss-' . now()->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // CASH FLOW
    // =========================================================================

    public function cashFlowExcel(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getCashFlow($company->id, $start, $end, $branchId);

        return Excel::download(new CashFlowExport($company, $report, $start, $end, $branches, $branchId), 'cash-flow-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function cashFlowPdf(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getCashFlow($company->id, $start, $end, $branchId);

        $pdf = Pdf::loadView('exports.cash-flow', compact('company', 'report', 'start', 'end', 'branches', 'branchId'));
        return $pdf->download('cash-flow-' . now()->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // GENERAL LEDGER
    // =========================================================================

    public function generalLedgerExcel(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        [$start, $end] = $this->resolveDateRange($request);

        $report = null;
        $account = null;

        if ($request->filled('account_id')) {
            $account = Account::findOrFail($request->account_id);
            $report = $this->reportService->getGeneralLedger($account, $start, $end, $branchId);
        }

        if (!$report) {
            abort(400, 'Please select an account to generate the General Ledger report.');
        }

        return Excel::download(new GeneralLedgerExport($company, $report, $account, $start, $end, $branches, $branchId), 'general-ledger-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function generalLedgerPdf(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        [$start, $end] = $this->resolveDateRange($request);

        $report = null;
        $account = null;

        if ($request->filled('account_id')) {
            $account = Account::findOrFail($request->account_id);
            $report = $this->reportService->getGeneralLedger($account, $start, $end, $branchId);
        }

        if (!$report) {
            abort(400, 'Please select an account to generate the General Ledger report.');
        }

        $pdf = Pdf::loadView('exports.general-ledger', compact('company', 'report', 'account', 'start', 'end', 'branches', 'branchId'))->setPaper('a4', 'landscape');
        return $pdf->download('general-ledger-' . now()->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // TRIAL BALANCE
    // =========================================================================

    public function trialBalanceExcel(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getTrialBalance($company->id, $start, $end, $branchId);

        return Excel::download(new \App\Exports\TrialBalanceExport($company, $report, $start, $end, $branches, $branchId), 'trial-balance-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function trialBalancePdf(Request $request)
    {
        $company  = Auth::user()->company;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $branches = Branch::all();
        [$start, $end] = $this->resolveDateRange($request);

        $report = $this->reportService->getTrialBalance($company->id, $start, $end, $branchId);

        $pdf = Pdf::loadView('exports.trial-balance', compact('company', 'report', 'start', 'end', 'branches', 'branchId'));
        return $pdf->download('trial-balance-' . now()->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // RENTALS REPORT
    // =========================================================================

    public function rentalsExcel(Request $request)
    {
        $company = Auth::user()->company;
        $status = $request->status;

        return Excel::download(new \App\Exports\RentalExport($company, $status), 'rentals-report-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function rentalsPdf(Request $request)
    {
        $company = Auth::user()->company;
        $status = $request->status;

        $rentals = Rental::when($status, fn($q) => $q->where('status', $status))
            ->with(['vehicle', 'customer', 'driver'])
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('exports.rentals-pdf', compact('company', 'rentals', 'status'))->setPaper('a4', 'landscape');
        return $pdf->download('rentals-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // DRIVERS REPORT
    // =========================================================================

    public function driversExcel(Request $request)
    {
        $company = Auth::user()->company;
        $status = $request->status;

        return Excel::download(new \App\Exports\DriverExport($company, $status), 'drivers-report-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function driversPdf(Request $request)
    {
        $company = Auth::user()->company;
        $status = $request->status;

        $drivers = \App\Models\Driver::when($status, fn($q) => $q->where('status', $status))
            ->orderBy('first_name')
            ->get();

        $pdf = Pdf::loadView('exports.drivers', compact('company', 'drivers', 'status'));
        return $pdf->download('drivers-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // =========================================================================
    // VENDOR BILLS
    // =========================================================================

    private function getVendorBillsData(Request $request)
    {
        $company = Auth::user()->company;
        $query = VendorBill::query()
            ->with(['vendor', 'items.expenseAccount']);

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->orderBy('bill_date', 'desc')->get();
    }

    public function vendorBillsExcel(Request $request)
    {
        $company = Auth::user()->company;
        $vendorBills = $this->getVendorBillsData($request);
        return Excel::download(new VendorBillExport($company, $vendorBills), 'vendor-bills-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function vendorBillsPdf(Request $request)
    {
        $company = Auth::user()->company;
        $vendorBills = $this->getVendorBillsData($request);

        $pdf = Pdf::loadView('exports.vendor-bills', compact('company', 'vendorBills'))->setPaper('a4', 'landscape');
        return $pdf->download('vendor-bills-' . now()->format('Y-m-d') . '.pdf');
    }
}
