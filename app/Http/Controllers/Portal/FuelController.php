<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\FuelLog;
use App\Models\DriverTripReport;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FuelController extends Controller
{
    /**
     * Display a listing of fuel logs and pending reports.
     */
    public function index(Request $request)
    {
        $query = FuelLog::with(['vehicle', 'creator', 'branch'])
            ->orderBy('date', 'desc');

        // Filters
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $logs     = $query->paginate(20, ['*'], 'logs_page')->withQueryString();
        $vehicles = Vehicle::orderBy('vehicle_number')->get(['id', 'vehicle_number', 'name']);
        
        // Pending Driver Reports
        $pendingReports = DriverTripReport::where('type', DriverTripReport::TYPE_FUEL)
            ->where('status', DriverTripReport::STATUS_PENDING)
            ->with(['driver.user', 'vehicle', 'rental'])
            ->orderBy('reported_at', 'desc')
            ->get();

        $stats = [
            'total_liters' => FuelLog::sum('liters'),
            'total_cost'   => FuelLog::sum('total_amount'),
            'avg_price'    => FuelLog::avg('cost_per_liter'),
            'pending_cnt'  => $pendingReports->count(),
        ];

        return view('portal.fuel.index', compact('logs', 'vehicles', 'pendingReports', 'stats'));
    }

    /**
     * Store a new fuel log (or approve a driver report).
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'       => 'required|exists:tenant.vehicles,id',
            'date'             => 'required|date',
            'odometer_reading' => 'required|numeric|min:0',
            'liters'           => 'required|numeric|min:0.1',
            'cost_per_liter'   => 'required|numeric|min:0',
            'total_amount'     => 'required|numeric|min:0',
            'report_id'        => 'nullable|exists:tenant.driver_trip_reports,id',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $log = FuelLog::create([
            'vehicle_id'       => $request->vehicle_id,
            'branch_id'        => $vehicle->branch_id,
            'date'             => $request->date,
            'odometer_reading' => $request->odometer_reading,
            'liters'           => $request->liters,
            'cost_per_liter'   => $request->cost_per_liter,
            'total_amount'     => $request->total_amount,
            'created_by'       => auth()->user()?->id,
        ]);

        // If approving a report, mark it as completed
        if ($request->report_id) {
            DriverTripReport::where('id', $request->report_id)
                ->update(['status' => DriverTripReport::STATUS_COMPLETED]);
        }

        // Update vehicle odometer if higher
        if ($request->odometer_reading > ($vehicle->current_odometer ?? 0)) {
            $vehicle->update(['current_odometer' => $request->odometer_reading]);
        }

        return redirect()->route('company.fuel.index')
            ->with('success', 'Fuel log recorded successfully.');
    }

    /**
     * Show fuel log details.
     */
    public function show(FuelLog $fuel)
    {
        $fuel->load(['vehicle', 'creator', 'branch']);
        return view('portal.fuel.show', compact('fuel'));
    }

    /**
     * Delete a fuel log.
     */
    public function destroy(FuelLog $fuel)
    {
        $fuel->delete();
        return redirect()->route('company.fuel.index')
            ->with('success', 'Fuel log deleted.');
    }
}
