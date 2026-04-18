<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\FinancialTransaction;
use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    protected $transactionRepository;

    public function __construct(\App\Repositories\TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Financial Dashboard / P&L Summary
     */
    public function dashboard(Request $request)
    {
        $company = $request->user()->company;
        if (!$company) abort(403);

        $startDate = $request->date('start_date') ?? now()->startOfMonth();
        $endDate = $request->date('end_date') ?? now()->endOfMonth();

        $summary = $this->transactionRepository->getFinancialSummary($company, $startDate, $endDate);

        // Get recent transactions for widget
        $recentTransactions = $this->transactionRepository->getTransactions($company, [], 5);

        return view('portal.finance.dashboard', compact('summary', 'recentTransactions', 'startDate', 'endDate'));
    }

    /**
     * List all financial transactions.
     */
    public function transactions(Request $request)
    {
        $company = $request->user()->company;
        if (!$company) abort(403);

        $filters = $request->only(['date_from', 'date_to', 'reference_type', 'search']);
        $transactions = $this->transactionRepository->getTransactions($company, $filters);

        return view('portal.finance.transactions', compact('transactions', 'filters'));
    }

    /**
     * List Invoices (Rentals that are active/completed/billed).
     */
    public function index()
    {
        $invoices = Rental::on('tenant')->whereIn('status', ['confirmed', 'assigned', 'dispatched', 'completed', 'closed'])
            ->whereNotNull('start_date')
            ->where('final_amount', '>', 0) // Only billable rentals
            ->with(['customer', 'transactions']) // relationship to be added
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('portal.finance.invoices', compact('invoices'));
    }

    /**
     * Record a payment against a rental invoice.
     */
    public function storePayment(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,online',
            'reference' => 'nullable|string',
            'payment_date' => 'required|date',
        ]);

        DB::transaction(function () use ($rental, $validated) {
            // 1. ERP Accounting: Record Payment
            app(\App\Services\AccountingService::class)->recordPaymentReceived(
                $rental,
                (float) $validated['amount'],
                $validated['payment_method']
            );

            // 2. Update Rental Payment Status
            // (Simplified status logic maintained for now)
            if ($validated['amount'] >= $rental->final_amount) {
                $rental->update(['payment_status' => 'paid']);
            } else {
                $rental->update(['payment_status' => 'partially_paid']);
            }
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}
