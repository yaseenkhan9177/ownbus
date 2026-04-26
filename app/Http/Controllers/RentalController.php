<?php

namespace App\Http\Controllers;

use App\Traits\LogsEvents;
use App\Services\EventLoggerService;

use App\Http\Controllers\Controller;
use App\Http\Requests\RentalRequest;
use App\Http\Requests\ContractRequest;
use App\Models\Rental;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Repositories\RentalRepository;
use App\Services\RentalStateService;
use App\Services\RentalPriceCalculator;
use App\Services\Fleet\ContractDeploymentService;
use App\Exceptions\CreditLimitExceededException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class RentalController extends Controller
{
    use LogsEvents;

    protected $rentalRepository;
    protected $stateService;
    protected $priceCalculator;

    public function __construct(
        RentalRepository $rentalRepository,
        RentalStateService $stateService,
        RentalPriceCalculator $priceCalculator
    ) {
        $this->rentalRepository = $rentalRepository;
        $this->stateService = $stateService;
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * Helper to get company or abort if not found.
     */
    protected function getCompanyOrAbort()
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'User is not associated with any company.');
        }
        return $company;
    }

    /**
     * Display a listing of rentals.
     */
    public function index(Request $request)
    {
        $company = $this->getCompanyOrAbort();

        $filters = $request->only(['status', 'branch_id', 'search', 'date_from', 'date_to', 'vehicle_id']);

        // Fleet stats for tactical toolbar
        $opsService = app(\App\Services\Fleet\FleetOperationsService::class);
        $stats = $opsService->getRentalStats($company);

        $rentals = $this->rentalRepository->getRentals($company, $filters);

        return view('portal.rentals.index', compact('rentals', 'filters', 'stats'));
    }

    /**
     * Show the form for creating a new rental (Quote Builder).
     */
    public function create()
    {
        $company = $this->getCompanyOrAbort();
        $customers = Customer::all();
        // Only active vehicles for rentals
        $vehicles = Vehicle::where('status', Vehicle::STATUS_AVAILABLE)->get();
        $drivers = Driver::available()->get();

        return view('portal.rentals.create', compact('customers', 'vehicles', 'drivers'));
    }

    /**
     * Store a newly created rental.
     */
    public function store(RentalRequest $request)
    {
        $company   = $this->getCompanyOrAbort();
        $validated = $request->validated();

        // ── 7B: Credit Enforcement ───────────────────────────────────
        $customer = Customer::findOrFail($validated['customer_id']);
        if ($customer->isCreditBlocked()) {
            throw new CreditLimitExceededException($customer);
        }
        // ─────────────────────────────────────────────────────────────

        $data = [
            'uuid'             => (string) Str::uuid(),
            'branch_id'        => Auth::user()?->branch_id,
            'customer_id'      => $validated['customer_id'],
            'vehicle_id'       => $validated['vehicle_id'] ?? null,
            'driver_id'        => $validated['driver_id'] ?? null,
            'rental_type'      => $validated['rental_type'],
            'rate_type'        => $validated['rate_type'],
            'rate_amount'      => $validated['rate_amount'],
            'status'           => Rental::STATUS_DRAFT,
            'start_date'       => $validated['start_date'],
            'end_date'         => $validated['end_date'],
            'pickup_location'  => $validated['pickup_location'],
            'dropoff_location' => $validated['dropoff_location'],
            'security_deposit' => $validated['security_deposit'] ?? 0,
            'discount'         => $validated['discount'] ?? 0,
            'notes'            => $validated['notes'] ?? null,
            'created_by'       => Auth::id(),
        ];

        // Basic tax calculation (could be moved to service)
        $subtotal      = $validated['rate_amount'] - ($validated['discount'] ?? 0);
        $data['tax']          = $subtotal * 0.05; // 5% VAT
        $data['final_amount'] = $subtotal + $data['tax'];

        $rental = $this->rentalRepository->createRental($company, $data);

        $this->logEvent(
            $company,
            EventLoggerService::RENTAL_CREATED,
            $rental,
            "Rental #{$rental->rental_number} created for " . $customer->company_name,
            ['final_amount' => $rental->final_amount, 'rental_type' => $rental->rental_type]
        );

        $settings = $company->companyNotificationSettings;
        if ($settings && $settings->whatsapp_enabled && $settings->notify_new_rental && $settings->whatsapp_number) {
            \App\Jobs\SendWhatsAppJob::dispatch(
                $settings->whatsapp_number,
                'rental_created',
                [
                    'company_name' => $company->name,
                    'customer_name' => $customer->name ?? $customer->company_name,
                    'vehicle_name' => $rental->vehicle ? $rental->vehicle->vehicle_number : 'N/A',
                    'start_date' => \Carbon\Carbon::parse($rental->start_date)->format('d M Y'),
                    'end_date' => \Carbon\Carbon::parse($rental->end_date)->format('d M Y'),
                    'amount' => number_format($rental->final_amount, 2),
                ]
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'rental' => $rental->load('customer', 'vehicle'),
                'message' => 'Rental Quote created successfully.'
            ]);
        }

        return redirect()->route('company.rentals.show', $rental)
            ->with('success', 'Rental Quote created successfully.');
    }

    /**
     * Display the specified rental.
     */
    public function show(Rental $rental)
    {
        $company = $this->getCompanyOrAbort();

        // Ensure scoping
        $rental = $this->rentalRepository->findRental($company, $rental->id);

        return view('portal.rentals.show', compact('rental'));
    }

    /**
     * Show the form for editing the specified rental.
     */
    public function edit(Rental $rental)
    {
        $company = $this->getCompanyOrAbort();

        $customers = Customer::all();
        $vehicles = Vehicle::where('status', Vehicle::STATUS_AVAILABLE)->get();
        $drivers = Driver::available($rental->start_date, $rental->end_date, $rental->id)->get();

        return view('portal.rentals.edit', compact('rental', 'customers', 'vehicles', 'drivers'));
    }

    /**
     * Update the specified rental.
     */
    public function update(RentalRequest $request, Rental $rental)
    {
        $company = $this->getCompanyOrAbort();

        $validated = $request->validated();

        $data = [
            'customer_id' => $validated['customer_id'],
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'driver_id' => $validated['driver_id'] ?? null,
            'rental_type' => $validated['rental_type'],
            'rate_type' => $validated['rate_type'],
            'rate_amount' => $validated['rate_amount'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'pickup_location' => $validated['pickup_location'],
            'dropoff_location' => $validated['dropoff_location'],
            'security_deposit' => $validated['security_deposit'] ?? 0,
            'discount' => $validated['discount'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ];

        // Recalculate financial fields
        $subtotal = $data['rate_amount'] - $data['discount'];
        $data['tax'] = $subtotal * 0.05;
        $data['final_amount'] = $subtotal + $data['tax'];

        $this->rentalRepository->updateRental($rental, $data);

        $this->logEvent(
            $company,
            EventLoggerService::RENTAL_UPDATED,
            $rental,
            "Rental #{$rental->rental_number} details updated",
            ['changes' => $data]
        );

        return redirect()->route('company.rentals.show', $rental)
            ->with('success', 'Rental updated successfully.');
    }

    /**
     * Handle state transitions.
     */
    public function transition(Request $request, Rental $rental)
    {
        $request->validate([
            'to_status' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        try {
            $this->stateService->transition($rental, $request->to_status, $request->reason);

            $this->logEvent(
                Auth::user()->company,
                $request->to_status === 'completed' ? EventLoggerService::RENTAL_COMPLETED : EventLoggerService::RENTAL_UPDATED,
                $rental,
                "Rental #{$rental->rental_number} status changed to " . ucfirst($request->to_status),
                ['to_status' => $request->to_status, 'reason' => $request->reason],
                $request->to_status === 'overdue' ? EventLoggerService::SEVERITY_WARNING : EventLoggerService::SEVERITY_INFO
            );

            return back()->with('success', 'Rental status updated to ' . ucfirst($request->to_status));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified rental.
     */
    public function destroy(Rental $rental)
    {
        $company = $this->getCompanyOrAbort();

        if ($rental->status !== Rental::STATUS_DRAFT) {
            return back()->with('error', 'Only draft rentals can be deleted.');
        }

        $this->rentalRepository->deleteRental($rental);

        return redirect()->route('company.rentals.index')
            ->with('success', 'Rental deleted successfully.');
    }

    /**
     * Show the new unified contract form (5 sections).
     */
    public function contractForm()
    {
        $company = $this->getCompanyOrAbort();

        $branches = \App\Models\Branch::all();
        $customers = Customer::all();

        // Available vehicles and drivers
        $vehicles = Vehicle::where('status', Vehicle::STATUS_AVAILABLE)
            ->get();

        $drivers = Driver::available()
            ->get();

        // Auto-generate contract number
        $nextId = (Rental::max('id') ?? 0) + 1;
        $contractNo = 'CONT-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('portal.rentals.contract', compact('branches', 'customers', 'vehicles', 'drivers', 'contractNo'));
    }

    /**
     * Store the new unified contract.
     */
    public function storeContract(ContractRequest $request, ContractDeploymentService $deploymentService)
    {
        $company = $this->getCompanyOrAbort();
        $validated = $request->validated();

        try {
            $rental = $deploymentService->deploy($validated, $company);

            return redirect()->route('company.rentals.show', $rental)
                ->with('success', 'Contract #' . $rental->contract_no . ' created and deployed successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to deploy contract: ' . $e->getMessage());
        }
    }

    /**
     * Confirm the rental mission.
     */
    public function confirm($id)
    {
        $company = $this->getCompanyOrAbort();
        $rental = Rental::with(['customer', 'vehicle', 'driver'])->findOrFail($id);

        if ($rental->status !== Rental::STATUS_DRAFT) {
            return back()->with('error', 'Only draft rentals can be confirmed.');
        }

        if (!$rental->vehicle_id) {
            return back()->with('error', 'Please assign a vehicle before confirming.');
        }

        try {
            $this->stateService->transition($rental, Rental::STATUS_CONFIRMED);

            $this->logEvent(
                $company,
                EventLoggerService::RENTAL_UPDATED,
                $rental,
                "Rental #{$rental->rental_number} confirmed and finalized",
                ['status' => Rental::STATUS_CONFIRMED]
            );

            // Auto-generate PDF and Send Notifications
            $this->generateAndSendPdf($rental);

            return back()->with('success', 'Rental confirmed, PDF generated, and customer notified via WhatsApp.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Complete the rental mission.
     */
    public function complete($id)
    {
        $company = $this->getCompanyOrAbort();
        $rental = Rental::findOrFail($id);

        try {
            $this->stateService->transition($rental, Rental::STATUS_COMPLETED);

            $this->logEvent(
                $company,
                EventLoggerService::RENTAL_COMPLETED,
                $rental,
                "Rental #{$rental->rental_number} marked as completed",
                ['status' => Rental::STATUS_COMPLETED]
            );

            return back()->with('success', 'Rental mission completed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Assign vehicle to an existing rental.
     */
    public function assignVehicle(Request $request, $id)
    {
        $company = $this->getCompanyOrAbort();
        $rental = Rental::findOrFail($id);

        $request->validate([
            'vehicle_id' => 'required|exists:tenant.vehicles,id'
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Check availability
        $opsService = app(\App\Services\Fleet\FleetOperationsService::class);
        if (!$opsService->checkVehicleAvailability($vehicle, $rental->start_date, $rental->end_date, $rental->id)) {
            return back()->with('error', 'This vehicle is already booked for the selected dates.');
        }

        $rental->update(['vehicle_id' => $vehicle->id]);

        $this->logEvent(
            $company,
            EventLoggerService::RENTAL_UPDATED,
            $rental,
            "Vehicle {$vehicle->vehicle_number} assigned to Rental #{$rental->rental_number}",
            ['vehicle_id' => $vehicle->id]
        );

        return back()->with('success', 'Vehicle assigned successfully.');
    }

    /**
     * Generate and download PDF.
     */
    public function generatePdf($id)
    {
        $rental = Rental::with(['customer', 'vehicle', 'driver', 'company'])->findOrFail($id);

        $pdf = Pdf::loadView('portal.rentals.pdf', compact('rental'));
        $pdf->setPaper('A4', 'portrait');

        // Save to storage
        $path = "rentals/{$rental->rental_number}.pdf";
        Storage::put("public/{$path}", $pdf->output());

        // Update rental with pdf path
        $rental->update(['pdf_path' => $path]);

        return $pdf->download("Rental-{$rental->rental_number}.pdf");
    }

    /**
     * Generate PDF and send via WhatsApp/Email.
     */
    private function generateAndSendPdf($rental)
    {
        // Ensure relationships are loaded
        $rental->load(['customer', 'vehicle', 'driver', 'company']);

        // Generate PDF
        $pdf = Pdf::loadView('portal.rentals.pdf', compact('rental'));
        $pdf->setPaper('A4', 'portrait');

        $path = "rentals/{$rental->rental_number}.pdf";
        Storage::put("public/{$path}", $pdf->output());

        // Update rental record
        $rental->update(['pdf_path' => $path]);

        // Send WhatsApp
        if ($rental->customer && $rental->customer->phone) {
            $whatsapp = new WhatsAppService();
            $message = "✅ *RENTAL CONFIRMED*\n\n" .
                "Dear {$rental->customer->name},\n\n" .
                "Your rental has been confirmed!\n\n" .
                "🚌 Vehicle: " . ($rental->vehicle ? $rental->vehicle->vehicle_number : 'N/A') . "\n" .
                "📅 From: " . $rental->start_date->format('d M Y') . "\n" .
                "📅 To: " . $rental->end_date->format('d M Y') . "\n" .
                "💰 Total: AED " . number_format($rental->final_amount, 2) . "\n\n" .
                "Login to download receipt:\n" .
                "ownbus.software\n\n" .
                "_OwnBus Fleet Management_";

            $whatsapp->send($rental->customer->phone, $message);
        }

        // Send Email (placeholder - implement if needed)
        // Mail::to($rental->customer->email)->send(new RentalConfirmedMail($rental, $path));
    }
}
