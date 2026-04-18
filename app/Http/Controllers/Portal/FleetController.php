<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use App\Repositories\VehicleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FleetController extends Controller
{
    protected VehicleRepository $vehicleRepository;
    protected \App\Repositories\MaintenanceRepository $maintenanceRepository;
    protected \App\Services\Fleet\MaintenanceService $maintenanceService;

    public function __construct(
        VehicleRepository $vehicleRepository,
        \App\Repositories\MaintenanceRepository $maintenanceRepository,
        \App\Services\Fleet\MaintenanceService $maintenanceService
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->maintenanceRepository = $maintenanceRepository;
        $this->maintenanceService = $maintenanceService;
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
     * Display a listing of the vehicles.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Vehicle::class);
        $company = $this->getCompanyOrAbort();

        $filters = $request->only(['status', 'branch_id', 'search']);
        $vehicles = $this->vehicleRepository->getVehicles($company, $filters);

        $branches = \App\Models\Branch::all();

        return view('portal.fleet.index', compact('vehicles', 'filters', 'branches'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        $this->authorize('create', Vehicle::class);
        return view('portal.fleet.create');
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(VehicleRequest $request)
    {
        $this->authorize('create', Vehicle::class);
        $company = $this->getCompanyOrAbort();

        DB::transaction(function () use ($request, $company) {
            $data = $request->validated();

            // Set defaults for optional fields
            $data['status']       = $data['status'] ?? 'available';
            $data['transmission'] = $data['transmission'] ?? 'manual';

            // Handle image upload if present
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('vehicles', 'public');
                $data['image_path'] = $path;
                unset($data['image']); // don't try to mass-assign the file object
            }

            $this->vehicleRepository->createVehicle($company, $data);

            // Log activity
            \Illuminate\Support\Facades\Log::info('Created vehicle', ['vehicle_number' => $data['vehicle_number'], 'company_id' => $company->id]);
        });

        return redirect()->route('company.fleet.index')
            ->with('success', 'Vehicle created successfully.');
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle, \App\Services\TimelineService $timelineService)
    {
        $this->authorize('view', $vehicle);
        $company = $this->getCompanyOrAbort();

        // Use repository to get detailed view
        $vehicle = $this->vehicleRepository->findVehicle($company, $vehicle->id);

        // Load maintenance logs
        $logs = $this->maintenanceRepository->getMaintenanceLogs($company, ['vehicle_id' => $vehicle->id]);
        $maintenanceLogs = $logs->items();

        // Manually load creators from central database
        $userIds = collect($maintenanceLogs)->pluck('created_by')->filter()->unique();
        if ($userIds->isNotEmpty()) {
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
            foreach ($maintenanceLogs as $log) {
                if ($log->created_by && isset($users[$log->created_by])) {
                    $log->setRelation('creator', $users[$log->created_by]);
                }
            }
        }

        // Predict next service
        $predictedServiceDate = $this->maintenanceService->predictNextServiceDate($vehicle);

        // Fetch unified activity timeline
        $timeline = $timelineService->getVehicleTimeline($vehicle->id);

        return view('portal.fleet.show', compact('vehicle', 'maintenanceLogs', 'predictedServiceDate', 'timeline'));
    }

    /**
     * Display a global listing of maintenance logs.
     */
    public function maintenanceIndex(Request $request)
    {
        $this->authorize('viewAny', Vehicle::class);
        $company = $this->getCompanyOrAbort();

        $filters = $request->only(['status', 'vehicle_id', 'reason_type']);
        $logs = $this->maintenanceRepository->getMaintenanceLogs($company, $filters);

        // Manually load creators to avoid cross-connection error
        $userIds = $logs->pluck('created_by')->filter()->unique();
        if ($userIds->isNotEmpty()) {
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
            $logs->each(function ($log) use ($users) {
                if ($log->created_by && isset($users[$log->created_by])) {
                    $log->setRelation('creator', $users[$log->created_by]);
                }
            });
        }

        // Also get upcoming maintenance
        $upcoming = $this->maintenanceRepository->getUpcomingMaintenance($company);

        return view('portal.fleet.maintenance', compact('logs', 'filters', 'upcoming'));
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);
        return view('portal.fleet.edit', compact('vehicle'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        DB::transaction(function () use ($request, $vehicle) {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('vehicles', 'public');
                $data['image_path'] = $path;
            }

            $this->vehicleRepository->updateVehicle($vehicle, $data);
        });

        return redirect()->route('company.fleet.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $this->authorize('delete', $vehicle);

        // Check if vehicle has active rentals or history that prevents deletion
        if ($vehicle->rentals()->exists()) {
            return back()->with('error', 'Cannot delete vehicle with rental history. Deactivate it instead.');
        }

        $this->vehicleRepository->deleteVehicle($vehicle);

        return redirect()->route('company.fleet.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    /**
     * Store maintenance log.
     */
    public function storeMaintenance(Request $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'reason_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $this->maintenanceService->scheduleMaintenance($vehicle, $validated, Auth::user());

        return back()->with('success', 'Maintenance scheduled successfully.');
    }
}
