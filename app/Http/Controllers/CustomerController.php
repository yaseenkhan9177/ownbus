<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Branch;
use App\Repositories\CustomerRepository;
use App\Services\Fleet\CustomerProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    protected $customerRepository;
    protected $profileService;

    public function __construct(
        CustomerRepository $customerRepository,
        CustomerProfileService $profileService
    ) {
        $this->customerRepository = $customerRepository;
        $this->profileService = $profileService;
    }

    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403);
        }

        $filters = $request->only(['type', 'search', 'status']);
        $customers = $this->customerRepository->getCustomers($company, $filters);

        return view('portal.customers.index', compact('customers', 'filters'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $branches = Branch::all();

        return view('portal.customers.create', compact('branches'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        // Support for Quick Add (AJAX)
        if ($request->has('name') && !$request->has('first_name')) {
            $parts = explode(' ', $request->input('name'), 2);
            $request->merge([
                'first_name' => $parts[0],
                'last_name' => $parts[1] ?? '',
                'type' => Customer::TYPE_INDIVIDUAL // Default for quick add
            ]);
        }

        $validated = $request->validate([
            'type' => ['required', Rule::in([Customer::TYPE_INDIVIDUAL, Customer::TYPE_CORPORATE])],
            'branch_id' => 'nullable|exists:tenant.branches,id',
            'first_name' => 'required_if:type,individual|nullable|string|max:255',
            'last_name' => 'required_if:type,individual|nullable|string|max:255',
            'company_name' => 'required_if:type,corporate|nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('tenant.customers')],
            'alternate_phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'driving_license_no' => 'nullable|string|max:50',
            'driving_license_expiry' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'cnic_no' => 'nullable|string|max:50', // Added for consistency with JS
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = Customer::STATUS_ACTIVE;

        $customer = Customer::create($validated);

        if ($request->wantsJson()) {
            return response()->json($customer);
        }

        return redirect()->route('company.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer profile (CRM Dashboard).
     */
    public function show(Customer $customer)
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403);
        }

        // Eager load recent activity
        $customer->load(['rentals' => function ($q) {
            $q->latest()->limit(10);
        }, 'documents', 'branch']);

        // Manual load creator (central DB)
        if ($customer->created_by) {
            $customer->setRelation('creator', \App\Models\User::find($customer->created_by));
        }

        $metrics = $this->profileService->getCustomerMetrics($customer);

        // Construct Ledger Statement (A/R Account 1013)
        $arAccountId = \App\Models\Account::where('account_code', '1013')->value('id');
        $ledgerLines = [];
        
        if ($arAccountId) {
            $ledgerLines = \App\Models\JournalEntryLine::where('account_id', $arAccountId)
                ->whereHas('journalEntry', function($query) use ($customer) {
                    $query->where('reference_type', \App\Models\Rental::class)
                          ->whereIn('reference_id', $customer->rentals->pluck('id'));
                })
                ->with(['journalEntry'])
                ->latest('created_at')
                ->get();
        }

        return view('portal.customers.show', compact('customer', 'metrics', 'ledgerLines'));
    }

    /**
     * Show the form for editing the customer.
     */
    public function edit(Customer $customer)
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403);
        }

        $branches = Branch::all();

        return view('portal.customers.edit', compact('customer', 'branches'));
    }

    /**
     * Update the customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $companyId = Auth::user()->company_id;

        $validated = $request->validate([
            'type' => ['required', Rule::in([Customer::TYPE_INDIVIDUAL, Customer::TYPE_CORPORATE])],
            'branch_id' => 'nullable|exists:tenant.branches,id',
            'first_name' => 'required_if:type,individual|nullable|string|max:255',
            'last_name' => 'required_if:type,individual|nullable|string|max:255',
            'company_name' => 'required_if:type,corporate|nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('tenant.customers')->ignore($customer->id)],
            'alternate_phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'driving_license_no' => 'nullable|string|max:50',
            'driving_license_expiry' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in([Customer::STATUS_ACTIVE, Customer::STATUS_BLACKLISTED, Customer::STATUS_INACTIVE])],
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('company.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer (Soft Delete).
     */
    public function destroy(Customer $customer)
    {

        $customer->delete();

        return redirect()->route('company.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
