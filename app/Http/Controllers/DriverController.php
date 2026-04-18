<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use App\Repositories\DriverRepository;
use App\Services\Fleet\PerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    protected $driverRepository;
    protected $performanceService;

    public function __construct(
        DriverRepository $driverRepository,
        PerformanceService $performanceService
    ) {
        $this->driverRepository = $driverRepository;
        $this->performanceService = $performanceService;
    }

    /**
     * Display a listing of drivers.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403);
        }

        $filters = $request->only(['status', 'search', 'license_expiring_soon', 'license_expired']);
        $drivers = $this->driverRepository->getDrivers($company, $filters);

        return view('portal.drivers.index', compact('drivers', 'filters'));
    }

    /**
     * Show the form for creating a new driver.
     */
    public function create()
    {
        return view('portal.drivers.create');
    }

    /**
     * Store a newly created driver in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->company;

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'national_id' => 'required|string|max:50',
            'license_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tenant.drivers', 'license_number'),
            ],
            'license_expiry_date' => 'required|date',
            'license_type' => 'required|string|max:50',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'commission_rate' => 'nullable|numeric|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $nextId = (Driver::max('id') ?? 0) + 1;
        $driverCode = 'DRV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $driver = Driver::create(array_merge($validated, [
            'company_id' => $company->id,
            'driver_code' => $driverCode,
            'status' => Driver::STATUS_ACTIVE,
            'created_by' => Auth::id(),
        ]));

        return redirect()->route('company.drivers.show', $driver)
            ->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified driver profile.
     */
    public function show(Driver $driver)
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403);
        }

        $driver->load(['branch']);
        $driver->setRelation('creator', User::find($driver->created_by));

        // Use performance service if available, otherwise mock/default
        $metrics = $this->performanceService->getDriverMetrics($driver);
        $recentrentals = $this->driverRepository->getDriverRentals($driver, 10);

        return view('portal.drivers.show', compact('driver', 'metrics', 'recentrentals'));
    }

    /**
     * Show the form for editing the specified driver.
     */
    public function edit(Driver $driver)
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403);
        }

        return view('portal.drivers.edit', compact('driver'));
    }

    /**
     * Update the specified driver in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'national_id' => 'required|string|max:50',
            'license_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tenant.drivers', 'license_number')->ignore($driver->id),
            ],
            'license_expiry_date' => 'required|date',
            'license_type' => 'required|string|max:50',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'commission_rate' => 'nullable|numeric|max:100',
            'status' => 'required|in:active,suspended,inactive',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $driver->update($validated);

        return redirect()->route('company.drivers.show', $driver)
            ->with('success', 'Driver updated successfully.');
    }

    /**
     * Suspend/Activate driver.
     */
    public function toggleStatus(Driver $driver)
    {
        $newStatus = $driver->status === Driver::STATUS_ACTIVE
            ? Driver::STATUS_SUSPENDED
            : Driver::STATUS_ACTIVE;

        $driver->update(['status' => $newStatus]);

        return back()->with('success', "Driver " . ucfirst($newStatus) . " successfully.");
    }
}
