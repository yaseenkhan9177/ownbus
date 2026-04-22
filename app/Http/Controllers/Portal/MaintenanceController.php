<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\VehicleServiceInterval;
use App\Models\VehicleMaintenancePrediction;
use App\Services\Fleet\MaintenanceService;
use App\Services\PredictiveMaintenanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    protected MaintenanceService $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    public function index()
    {
        // /company/maintenance
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $companyId = $user->company_id;

        $records = MaintenanceRecord::with(['vehicle', 'vendor'])
            ->latest()
            ->paginate(15);

        // Top KPIs
        $kpis = [
            'scheduled' => MaintenanceRecord::where('status', 'scheduled')->count(),
            'in_progress' => MaintenanceRecord::where('status', 'in_progress')->count(),
            'overdue' => VehicleServiceInterval::where('next_due_date', '<', now())
                ->count(),
            'total_cost_month' => MaintenanceRecord::where('status', 'completed')
                ->whereMonth('completed_date', now()->month)
                ->sum('total_cost'),
        ];

        return view('portal.maintenance.index', compact('records', 'kpis'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $companyId = $user->company_id;
        $vehicles = Vehicle::where('status', '!=', 'inactive')->get();
        // Assuming vendors are tracked as customers with some flag, or just all customers for MVP
        $vendors = Customer::all();

        return view('portal.maintenance.create', compact('vehicles', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:tenant.vehicles,id',
            'type' => 'required|in:preventive,corrective,accident,inspection,insurance',
            'status' => 'required|in:scheduled,in_progress,completed',
            'scheduled_date' => 'nullable|date',
            'vendor_id' => 'nullable|exists:tenant.vendors,id',
            'odometer_reading' => 'nullable|numeric',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_type' => 'required|in:part,labor,service',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $data = $request->except('items');
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data['created_by'] = $user->id;

        if ($data['status'] === 'in_progress' && empty($data['start_date'])) {
            $data['start_date'] = now();
        } elseif ($data['status'] === 'completed' && empty($data['completed_date'])) {
            $data['completed_date'] = now();
        }

        $items = $request->input('items', []);

        if ($data['status'] === 'completed') {
            // Use specialized completeRecord flow if it's created as completed instantly
            $record = $this->maintenanceService->createRecord($data, $items);
            $this->maintenanceService->completeRecord($record, ['completed_date' => $data['completed_date'] ?? now()]);
        } else {
            $record = $this->maintenanceService->createRecord($data, $items);
        }

        $company = auth()->user()->company;
        $settings = $company->companyNotificationSettings;
        if ($data['status'] === 'scheduled' && $settings && $settings->whatsapp_enabled && $settings->notify_maintenance && $settings->whatsapp_number) {
            $vehicle = \App\Models\Vehicle::find($data['vehicle_id']);
            \App\Jobs\SendWhatsAppJob::dispatch(
                $settings->whatsapp_number,
                'maintenance_due',
                [
                    'company_name' => $company->name,
                    'vehicle_name' => $vehicle ? $vehicle->vehicle_number : 'N/A',
                    'service_type' => ucfirst($data['type']),
                    'due_date' => \Carbon\Carbon::parse($data['scheduled_date'] ?? now())->format('d M Y'),
                    'garage_name' => 'Designated Workshop',
                ]
            );
        }

        return redirect()->route('company.maintenance.index')->with('success', 'Maintenance record created successfully.');
    }

    public function show(MaintenanceRecord $maintenance)
    {
        $this->authorizeRecord($maintenance);
        $maintenance->load(['vehicle', 'items', 'vendor']);
        // creator is a User (central DB), cannot eager-load via tenant connection
        $maintenance->setRelation('creator', \App\Models\User::find($maintenance->created_by));

        return view('portal.maintenance.show', compact('maintenance'));
    }

    public function complete(Request $request, MaintenanceRecord $maintenance)
    {
        $this->authorizeRecord($maintenance);

        $request->validate([
            'odometer_reading' => 'required|numeric',
            'completed_date' => 'nullable|date',
        ]);

        $this->maintenanceService->completeRecord($maintenance, [
            'odometer_reading' => $request->odometer_reading,
            'completed_date' => $request->completed_date ?? now(),
        ]);

        return redirect()->route('company.maintenance.show', $maintenance)->with('success', 'Maintenance marked as completed and accounting entry generated.');
    }

    public function cancel(MaintenanceRecord $maintenance)
    {
        $this->authorizeRecord($maintenance);

        $maintenance->update(['status' => 'cancelled']);

        return redirect()->route('company.maintenance.index')->with('success', 'Maintenance cancelled.');
    }

    public function schedule()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $companyId = $user->company_id;

        $intervals = VehicleServiceInterval::with('vehicle')
            ->where(function ($q) {
                $q->where('next_due_date', '<=', now()->addDays(7))
                    ->orWhereNull('next_due_date'); // for those depending purely on KM, we'll check collection side
            })
            ->get();

        // KM based ones where vehicle current odo is within 500km of next_due
        $vehicles = Vehicle::all()->keyBy('id');

        $kmIntervals = VehicleServiceInterval::with('vehicle')
            ->whereNotNull('next_due_odometer')
            ->get()
            ->filter(function ($interval) use ($vehicles) {
                $vehicle = $vehicles->get($interval->vehicle_id);
                if (!$vehicle) return false;
                return ($interval->next_due_odometer - $vehicle->current_odometer) <= 500;
            });

        // Merge collections uniquely
        $schedules = $intervals->merge($kmIntervals)->unique('id');

        return view('portal.maintenance.schedule', compact('schedules'));
    }

    public function predictions()
    {
        $predictions = VehicleMaintenancePrediction::with('vehicle')
            ->orderByRaw("CASE risk_level WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->get();

        return view('portal.maintenance.predictions', compact('predictions'));
    }

    public function runAnalysis(PredictiveMaintenanceService $service)
    {
        $service->generatePredictions();

        return redirect()->route('company.maintenance.predictions')
            ->with('success', 'Predictive maintenance analysis completed for all vehicles.');
    }

    protected function authorizeRecord(MaintenanceRecord $record)
    {
        // In a tenant-isolated database, any record found belongs to the current tenant context.
    }
}
