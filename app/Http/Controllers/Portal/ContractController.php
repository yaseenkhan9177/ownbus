<?php

namespace App\Http\Controllers\Portal;

use App\Traits\LogsEvents;
use App\Services\EventLoggerService;
use App\Exceptions\CreditLimitExceededException;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Services\Billing\ContractBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Portal Contract Controller
 *
 * Manages the full contract lifecycle: create, view, approve, terminate.
 * Every store/update is guarded by credit block enforcement.
 */
class ContractController extends Controller
{
    use LogsEvents;

    protected ContractBillingService $billing;

    public function __construct(ContractBillingService $billing)
    {
        $this->billing = $billing;
    }

    public function index(Request $request)
    {
        $company = $this->getCompanyOrAbort();

        $contracts = Contract::with(['customer', 'vehicle', 'driver'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where('contract_number', 'like', "%{$s}%")
                ->orWhereHas('customer', fn($c) => $c->where('company_name', 'like', "%{$s}%")
                    ->orWhere('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('portal.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $company   = $this->getCompanyOrAbort();
        $customers = Customer::where('status', 'active')->orderBy('company_name')->orderBy('first_name')->get();
        $vehicles  = Vehicle::whereIn('status', ['available', 'active'])->orderBy('vehicle_number')->get();
        $drivers   = Driver::where('status', 'active')->orderBy('first_name')->orderBy('last_name')->get();

        return view('portal.contracts.create', compact('customers', 'vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $company   = $this->getCompanyOrAbort();
        $validated = $request->validate($this->rules());

        // ── 8A: Credit Enforcement Guard ─────────────────────────────
        $customer = Customer::findOrFail($validated['customer_id']);
        if ($customer->isCreditBlocked()) {
            throw new CreditLimitExceededException($customer);
        }
        // ─────────────────────────────────────────────────────────────

        $contract = Contract::create(array_merge($validated, [
            'branch_id'       => Auth::user()?->branch_id,
            'contract_number' => 'CON-' . strtoupper(Str::random(8)),
            'status'          => 'draft',
            'created_by'      => Auth::id(),
        ]));

        // Handle Document Uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('contracts/documents', 'public');
                $contract->documents()->create([
                    'document_type' => 'Agreement', // Default or from input
                    'file_path'     => $path,
                    'file_name'     => $file->getClientOriginalName(),
                    'file_type'     => $file->getClientMimeType(),
                ]);
            }
        }

        $this->logEvent(
            $company,
            EventLoggerService::CONTRACT_ACTIVE,
            $contract,
            "Contract {$contract->contract_number} drafted for " . ($customer->company_name ?: $customer->name),
            ['contract_value' => $contract->contract_value]
        );

        return redirect()->route('company.contracts.show', $contract)
            ->with('success', "Contract {$contract->contract_number} created successfully.");
    }

    public function show(Contract $contract)
    {
        $company = $this->getCompanyOrAbort();
        $this->authorizeContract($contract);
        $contract->load(['customer', 'vehicle', 'driver', 'invoices.journalEntry']);

        $billingStats = $this->billing->getTodayBillingStats($company->id);

        return view('portal.contracts.show', compact('contract', 'billingStats'));
    }

    public function edit(Contract $contract)
    {
        $this->authorizeContract($contract);
        $company   = $this->getCompanyOrAbort();
        $customers = Customer::orderBy('company_name')->orderBy('first_name')->get();
        $vehicles  = Vehicle::orderBy('vehicle_number')->get();
        $drivers   = Driver::orderBy('first_name')->orderBy('last_name')->get();

        return view('portal.contracts.edit', compact('contract', 'customers', 'vehicles', 'drivers'));
    }

    public function update(Request $request, Contract $contract)
    {
        $this->authorizeContract($contract);
        $validated = $request->validate($this->rules($contract->id));

        // ── 8A: Credit Enforcement Guard on customer change ──────────
        if ($validated['customer_id'] != $contract->customer_id) {
            $customer = Customer::findOrFail($validated['customer_id']);
            if ($customer->isCreditBlocked()) {
                throw new CreditLimitExceededException($customer);
            }
        }
        // ─────────────────────────────────────────────────────────────

        $contract->update($validated);

        // Handle Document Uploads (Additive)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('contracts/documents', 'public');
                $contract->documents()->create([
                    'document_type' => 'Attachment',
                    'file_path'     => $path,
                    'file_name'     => $file->getClientOriginalName(),
                    'file_type'     => $file->getClientMimeType(),
                ]);
            }
        }

        return redirect()->route('company.contracts.show', $contract)
            ->with('success', 'Contract updated successfully.');
    }

    /**
     * Download the contract as a PDF.
     */
    public function downloadContract(Contract $contract)
    {
        $company = $this->getCompanyOrAbort();
        $this->authorizeContract($contract);
        $contract->load(['customer', 'vehicle', 'driver']);

        $pdf = Pdf::loadView('exports.contract-pdf', compact('company', 'contract'));
        return $pdf->download('contract-' . $contract->contract_number . '.pdf');
    }

    /**
     * Activate a draft contract.
     */
    public function activate(Contract $contract)
    {
        $this->authorizeContract($contract);

        // Re-check credit on activation
        $contract->load('customer');
        if ($contract->customer->isCreditBlocked()) {
            throw new CreditLimitExceededException($contract->customer);
        }

        $contract->update(['status' => 'active']);

        $this->logEvent(
            $this->getCompanyOrAbort(),
            EventLoggerService::CONTRACT_ACTIVE,
            $contract,
            "Contract {$contract->contract_number} activated",
            ['activated_at' => now()]
        );

        return back()->with('success', "Contract {$contract->contract_number} activated.");
    }

    /**
     * Terminate a contract early.
     */
    public function terminate(Request $request, Contract $contract)
    {
        $this->authorizeContract($contract);

        $contract->update([
            'status'   => 'terminated',
            'end_date' => now()->toDateString(),
            'notes'    => $contract->notes . "\n[Terminated " . now()->format('d M Y') . ': ' . $request->reason . ']',
        ]);

        $this->logEvent(
            $this->getCompanyOrAbort(),
            EventLoggerService::CONTRACT_EXPIRED, // Using expired as termination
            $contract,
            "Contract {$contract->contract_number} terminated early",
            ['reason' => $request->reason],
            EventLoggerService::SEVERITY_WARNING
        );

        return redirect()->route('company.contracts.index')
            ->with('success', "Contract {$contract->contract_number} terminated.");
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'customer_id'       => 'required|integer|exists:tenant.customers,id',
            'vehicle_id'        => 'required|integer|exists:tenant.vehicles,id',
            'driver_id'         => 'nullable|integer|exists:tenant.drivers,id',
            'start_date'        => 'required|date',
            'start_time'        => 'nullable|string',
            'end_date'          => 'required|date|after:start_date',
            'end_time'          => 'nullable|string',
            'contract_value'    => 'required|numeric|min:0',
            'monthly_rate'      => 'nullable|numeric|min:0',
            'extra_charges'     => 'nullable|numeric|min:0',
            'discount'          => 'nullable|numeric|min:0',
            'billing_cycle'     => 'required|in:monthly,quarterly,yearly,custom',
            'payment_terms'     => 'nullable|string|max:500',
            'payment_due_date'  => 'nullable|date',
            'auto_renew'        => 'boolean',
            'terms'             => 'nullable|string|max:5000',
            'notes'             => 'nullable|string|max:2000',
            'documents.*'       => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:5120',
        ];
    }

    protected function getCompanyOrAbort()
    {
        $company = Auth::user()?->company;
        if (!$company) abort(403, 'No company context.');
        return $company;
    }

    protected function authorizeContract(Contract $contract): void
    {
        // In multi-database tenancy, being able to query the contract at all
        // implies it belongs to the current tenant/database context.
    }
}
