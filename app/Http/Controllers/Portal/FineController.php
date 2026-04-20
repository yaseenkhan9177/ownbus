<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Traits\LogsEvents;
use App\Services\EventLoggerService;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\VehicleFine;
use App\Services\FineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FineController extends Controller
{
    use LogsEvents;

    protected $fineService;

    public function __construct(FineService $fineService)
    {
        $this->fineService = $fineService;
    }

    public function index()
    {
        $companyId = Auth::user()->company_id;
        $fines = VehicleFine::query()
            ->when(Auth::user()->branch_id, fn($query, $branchId) => $query->where('branch_id', $branchId))
            ->with(['vehicle', 'driver', 'customer', 'rental'])
            ->latest()
            ->paginate(15);

        return view('portal.fines.index', compact('fines'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $branchId = Auth::user()->branch_id;
        $vehicles = Vehicle::when($branchId, fn($q) => $q->where('branch_id', $branchId))->where('status', '!=', 'inactive')->get();
        $drivers = Driver::when($branchId, fn($q) => $q->where('branch_id', $branchId))->where('status', '!=', 'inactive')->get();
        $customers = Customer::all();
        $rentals = \App\Models\Rental::when($branchId, fn($q) => $q->where('branch_id', $branchId))->whereIn('status', ['active', 'completed'])->with('customer')->latest()->get();

        return view('portal.fines.record', compact('vehicles', 'drivers', 'customers', 'rentals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:tenant.vehicles,id',
            'rental_id' => 'nullable|exists:tenant.rentals,id',
            'driver_id' => 'nullable|exists:tenant.drivers,id',
            'customer_id' => 'nullable|exists:tenant.customers,id',
            'responsible_type' => 'required|in:driver,customer,both,company',
            'fine_type' => 'required|string',
            'fine_number' => 'required|string|unique:tenant.vehicle_fines,fine_number',
            'fine_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:fine_date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB limit

            // Payment fields
            'payment_status' => 'required|string|in:pending,paid',
            'payment_method' => 'nullable|string|required_if:payment_status,paid',
        ]);

        $validated['branch_id'] = Auth::user()->branch_id ?? Vehicle::findOrFail($validated['vehicle_id'])->branch_id;
        $validated['status'] = $validated['payment_status'] === 'paid' ? 'paid' : 'pending';
        $validated['source'] = 'System'; // Default source
        $validated['authority'] = 'General'; // Make it general unless specifically captured

        if ($validated['payment_status'] === 'paid') {
            $validated['paid_at'] = now();
        }

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('fines', 'public');
            $validated['attachment_path'] = $path;
        }

        $fine = $this->fineService->createFine($validated);

        $this->logEvent(
            Auth::user()->company,
            EventLoggerService::FINE_ADDED,
            $fine,
            "New fine recorded for vehicle " . $fine->vehicle->vehicle_number . ": AED {$fine->amount}",
            ['amount' => $fine->amount, 'authority' => $fine->authority],
            $fine->amount > 2000 ? EventLoggerService::SEVERITY_WARNING : EventLoggerService::SEVERITY_INFO
        );

        return redirect()->route('company.fines.index')
            ->with('success', 'Fine recorded and linked to accounting.');
    }

    public function import()
    {
        return view('portal.fines.import');
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            
            // Basic mapping logic
            // Expected columns: vehicle_number, fine_number, fine_date, amount, authority, fine_type
            $vehicle = Vehicle::where('vehicle_number', $data['vehicle_number'])->first();
            if (!$vehicle) continue;

            $this->fineService->createFine([
                'vehicle_id' => $vehicle->id,
                'fine_number' => $data['fine_number'],
                'fine_date' => $data['fine_date'],
                'amount' => $data['amount'],
                'authority' => $data['authority'] ?? 'RTA',
                'fine_type' => $data['fine_type'] ?? 'General',
                'status' => 'unpaid',
                'responsible_type' => 'company', // Default for import
            ]);
            $count++;
        }
        fclose($handle);

        return redirect()->route('company.fines.index')->with('success', "Imported {$count} fines successfully.");
    }

    public function report(Request $request)
    {
        $query = VehicleFine::with(['vehicle', 'driver']);

        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->get('vehicle_id'));
        }

        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->get('driver_id'));
        }

        $fines = $query->get();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        return view('portal.fines.report', compact('fines', 'vehicles', 'drivers'));
    }
}
