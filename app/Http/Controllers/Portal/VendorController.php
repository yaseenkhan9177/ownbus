<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function __construct(protected VendorService $vendorService) {}

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $vendors = Vendor::query()
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                        ->orWhere('vendor_code', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%");
                })
            )
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('company.vendors.index', compact('vendors'));
    }

    // ─── Create / Store ───────────────────────────────────────────────────────

    public function create()
    {
        return view('company.vendors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_code'       => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('tenant.vendors', 'vendor_code'),
            ],
            'name'              => 'required|string|max:191',
            'contact_person'    => 'nullable|string|max:191',
            'phone'             => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:191',
            'tax_number'        => 'nullable|string|max:50',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string|max:100',
            'opening_balance'   => 'nullable|numeric|min:0',
            'balance_direction' => 'required_if:opening_balance,>0|nullable|in:payable,receivable',
            'status'            => 'required|in:active,suspended',
            'branch_id'         => 'nullable|exists:tenant.branches,id',
        ]);

        $data['created_by'] = Auth::id();
        $data['opening_balance'] = $data['opening_balance'] ?? 0;

        $vendor = $this->vendorService->createVendor($data);

        return redirect()
            ->route('company.vendors.show', $vendor)
            ->with('success', "Vendor '{$vendor->name}' created successfully.");
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Vendor $vendor)
    {
        $this->authorizeVendor($vendor);

        $vendor->load(['branch', 'bills' => function ($q) {
            $q->latest()->limit(10);
        }]);

        // creator is a User (central DB), cannot eager-load via tenant connection
        $vendor->setRelation('creator', \App\Models\User::find($vendor->created_by));

        $outstanding = $vendor->calculateOutstandingBalance();

        return view('company.vendors.show', compact('vendor', 'outstanding'));
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    public function edit(Vendor $vendor)
    {
        $this->authorizeVendor($vendor);
        return view('company.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $this->authorizeVendor($vendor);

        $data = $request->validate([
            'vendor_code'    => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('tenant.vendors', 'vendor_code')
                    ->ignore($vendor->id),
            ],
            'name'           => 'required|string|max:191',
            'contact_person' => 'nullable|string|max:191',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:191',
            'tax_number'     => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string|max:100',
            'status'         => 'required|in:active,suspended',
            'branch_id'      => 'nullable|exists:tenant.branches,id',
        ]);

        // Note: opening_balance is immutable after creation (would require a new journal entry)
        $this->vendorService->updateVendor($vendor, $data);

        return redirect()
            ->route('company.vendors.show', $vendor)
            ->with('success', "Vendor updated successfully.");
    }

    // ─── Suspend ──────────────────────────────────────────────────────────────

    public function suspend(Vendor $vendor)
    {
        $this->authorizeVendor($vendor);

        if ($vendor->isSuspended()) {
            $this->vendorService->activate($vendor);
            $msg = "Vendor '{$vendor->name}' reactivated.";
        } else {
            $this->vendorService->suspend($vendor);
            $msg = "Vendor '{$vendor->name}' suspended.";
        }

        return redirect()->back()->with('success', $msg);
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Vendor $vendor)
    {
        $this->authorizeVendor($vendor);

        if (!$this->vendorService->canDelete($vendor)) {
            return redirect()->back()->with(
                'error',
                "Cannot delete vendor '{$vendor->name}' — bills or journal entries exist. Suspend it instead."
            );
        }

        $name = $vendor->name;
        $vendor->delete();

        return redirect()
            ->route('company.vendors.index')
            ->with('success', "Vendor '{$name}' deleted.");
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    protected function authorizeVendor(Vendor $vendor): void
    {
        // In a tenant-isolated database, any vendor found belongs to the current tenant.
        // No manual company_id check is needed once the connection is switched.
    }
}
