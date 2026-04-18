<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Traits\LogsEvents;
use App\Services\EventLoggerService;
use App\Models\Expense;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\Vendor;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    use LogsEvents;

    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Expense::with(['vehicle', 'branch', 'vendor']);

        if ($request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);

        // Manually load creators to avoid cross-connection error
        $userIds = $expenses->pluck('created_by')->filter()->unique();
        if ($userIds->isNotEmpty()) {
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
            $expenses->each(function ($expense) use ($users) {
                if ($expense->created_by && isset($users[$expense->created_by])) {
                    $expense->setRelation('creator', $users[$expense->created_by]);
                }
            });
        }

        $vehicles = Vehicle::all();
        $branches = Branch::all();

        return view('portal.expenses.index', compact('expenses', 'vehicles', 'branches'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $vehicles = Vehicle::all();
        $branches = Branch::all();
        $vendors = Vendor::all();

        return view('portal.expenses.create', compact('vehicles', 'branches', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:tenant.branches,id',
            'vehicle_id' => 'nullable|exists:tenant.vehicles,id',
            'vendor_id' => 'nullable|exists:tenant.vendors,id',
            'category' => 'required|string',
            'description' => 'required|string',
            'amount_ex_vat' => 'required|numeric|min:0',
            'vat_percent' => 'required|numeric|min:0|max:100',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank,payable',
            'reference_no' => 'nullable|string',
        ]);

        // Auto-calculate vat_amount and total_amount server-side
        $validated['vat_amount'] = round($validated['amount_ex_vat'] * $validated['vat_percent'] / 100, 2);
        $validated['total_amount'] = round($validated['amount_ex_vat'] + $validated['vat_amount'], 2);


        if ($request->hasFile('invoice')) {
            $path = $request->file('invoice')->store('invoices', 'public');
            $validated['invoice_path'] = $path;
        }

        $expense = $this->expenseService->createExpense($validated);

        $this->logEvent(
            Auth::user()->company,
            EventLoggerService::EXPENSE_RECORDED,
            $expense,
            "Expense recorded: AED {$expense->total_amount} (" . ucfirst($expense->category) . ")",
            ['amount' => $expense->total_amount, 'category' => $expense->category],
            $expense->total_amount > 10000 ? EventLoggerService::SEVERITY_WARNING : EventLoggerService::SEVERITY_INFO
        );

        return redirect()->route('company.expenses.index')
            ->with('success', 'Expense recorded and journal entry created successfully.');
    }

    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        $expense->load(['vehicle', 'branch', 'vendor', 'journalEntries.lines.account']);
        // creator is a User (central DB), cannot eager-load via tenant connection
        $expense->setRelation('creator', \App\Models\User::find($expense->created_by));
        return view('portal.expenses.show', compact('expense'));
    }
}
