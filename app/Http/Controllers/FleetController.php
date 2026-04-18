<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Rental;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FleetController extends Controller
{
    public function index(Request $request)
    {
        // Simple Gantt/Calendar View
        // Show next 7 days for all vehicles

        $startDate = $request->has('date') ? Carbon::parse($request->date) : now();
        $endDate = $startDate->copy()->addDays(7);

        $vehicles = Vehicle::with(['bookings' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('pickup_time', [$startDate, $endDate])
                ->orWhereBetween('dropoff_time', [$startDate, $endDate]);
        }])
            ->orderBy('type')
            ->orderBy('vehicle_number')
            ->get();

        return view('fleet._index_legacy', compact('vehicles', 'startDate', 'endDate'));
    }

    public function maintenance()
    {
        $vehicles = Vehicle::all();
        $unavailabilities = \App\Models\VehicleUnavailability::with('vehicle')->orderBy('start_datetime', 'desc')->paginate(10);
        return view('fleet.maintenance', compact('vehicles', 'unavailabilities'));
    }

    public function storeMaintenance(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'reason_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        \App\Models\VehicleUnavailability::create([
            'vehicle_id' => $validated['vehicle_id'],
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'reason_type' => $validated['reason_type'],
            'description' => $validated['description'],
            'created_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return back()->with('success', 'Maintenance block created successfully.');
    }
}
