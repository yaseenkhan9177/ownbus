<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Vendor;
use App\Models\VendorBill;
use App\Services\Accounting\VendorBillService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorBillController extends Controller
{
    public function __construct(protected VendorBillService $billService) {}

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $bills = VendorBill::query()
            ->with(['vendor'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->vendor_id, fn($q, $v) => $q->where('vendor_id', $v))
            ->when($request->date_from, fn($q, $d) => $q->where('bill_date', '>=', $d))
            ->when($request->date_to, fn($q, $d) => $q->where('bill_date', '<=', $d))
            ->latest('bill_date')
            ->paginate(25)
            ->withQueryString();

        // Manually load creators from central database to avoid cross-connection error
        $userIds = $bills->pluck('created_by')->filter()->unique();
        if ($userIds->isNotEmpty()) {
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
            $bills->each(function ($bill) use ($users) {
                if ($bill->created_by && isset($users[$bill->created_by])) {
                    $bill->setRelation('creator', $users[$bill->created_by]);
                }
            });
        }

        $vendors = Vendor::active()->orderBy('name')->get();

        return view('company.vendor-bills.index', compact('bills', 'vendors'));
    }

    // ─── Create / Store (Draft) ────────────────────────────────────────────────

    public function create()
    {
        $companyId = Auth::user()->company_id;

        $vendors = Vendor::active()->orderBy('name')->get();

        // Only leaf expense accounts
        $expenseAccounts = Account::where('account_type', 'expense')
            ->where('is_active', true)
            ->get()
            ->filter(fn($acc) => $acc->isLeaf());

        $branches = \App\Models\Branch::all();

        return view('company.vendor-bills.create', compact('vendors', 'expenseAccounts', 'branches'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $data = $request->validate([
            'vendor_id'         => "required|exists:tenant.vendors,id",
            'bill_number'       => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('tenant.vendor_bills', 'bill_number'),
            ],
            'bill_date'         => 'required|date',
            'due_date'          => 'nullable|date|after_or_equal:bill_date',
            'tax_amount'        => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'branch_id'                  => 'nullable|exists:tenant.branches,id',
            'items'                      => 'required|array|min:1',
            'items.*.expense_account_id' => 'required|exists:tenant.accounts,id',
            'items.*.description'        => 'required|string|max:255',
            'items.*.quantity'           => 'required|numeric|min:0.01',
            'items.*.unit_cost'          => 'required|numeric|min:0',
        ]);

        // Verify vendor exists in tenant database
        $vendor = Vendor::where('id', $data['vendor_id'])
            ->firstOrFail();

        if ($vendor->isSuspended()) {
            return back()->withErrors(['vendor_id' => "Cannot create bill for suspended vendor: {$vendor->name}."]);
        }


        $bill = $this->billService->createDraft($data, $data['items']);

        return redirect()
            ->route('company.vendor-bills.show', $bill)
            ->with('success', "Bill #{$bill->bill_number} created as draft.");
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(VendorBill $vendorBill)
    {
        $this->authorizeBill($vendorBill);

        $vendorBill->load([
            'vendor',
            'items.expenseAccount',
            'journalEntries.lines.account'
        ]);

        // creator and approver are Users (central DB) — cannot eager-load via tenant connection
        $vendorBill->setRelation('creator', \App\Models\User::find($vendorBill->created_by));
        $vendorBill->setRelation('approver', \App\Models\User::find($vendorBill->approved_by));

        $paid      = $vendorBill->paidAmount();
        $remaining = $vendorBill->remainingAmount();

        return view('company.vendor-bills.show', compact('vendorBill', 'paid', 'remaining'));
    }

    // ─── Approve ──────────────────────────────────────────────────────────────

    public function approve(VendorBill $vendorBill)
    {
        $this->authorizeBill($vendorBill);

        try {
            $this->billService->approveBill($vendorBill);
            return redirect()
                ->route('company.vendor-bills.show', $vendorBill)
                ->with('success', "Bill #{$vendorBill->bill_number} approved. Journal entry posted.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ─── Record Payment ───────────────────────────────────────────────────────

    public function recordPayment(Request $request, VendorBill $vendorBill)
    {
        $this->authorizeBill($vendorBill);

        $data = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank',
        ]);

        try {
            $this->billService->recordPayment(
                $vendorBill,
                (float) $data['amount'],
                $data['payment_method']
            );
            return redirect()
                ->route('company.vendor-bills.show', $vendorBill)
                ->with('success', "Payment of {$data['amount']} recorded.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ─── Cancel ───────────────────────────────────────────────────────────────

    public function cancel(Request $request, VendorBill $vendorBill)
    {
        $this->authorizeBill($vendorBill);

        $data = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $this->billService->cancel($vendorBill, $data['reason'] ?? 'Cancelled by user');
            return redirect()
                ->route('company.vendor-bills.show', $vendorBill)
                ->with('success', "Bill #{$vendorBill->bill_number} cancelled.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(VendorBill $vendorBill)
    {
        $this->authorizeBill($vendorBill);

        try {
            $this->billService->deleteBill($vendorBill);
            return redirect()
                ->route('company.vendor-bills.index')
                ->with('success', "Bill #{$vendorBill->bill_number} deleted successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    protected function authorizeBill(VendorBill $bill): void
    {
        // In a tenant-isolated database, any bill found belongs to the current tenant.
    }
}
