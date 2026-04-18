<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleBrowseController extends Controller
{
    /**
     * Display public vehicle catalog with filters
     */
    public function index(Request $request): View
    {
        $query = Vehicle::where('status', 'active');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('capacity_min')) {
            $query->where('seating_capacity', '>=', $request->capacity_min);
        }

        if ($request->filled('capacity_max')) {
            $query->where('seating_capacity', '<=', $request->capacity_max);
        }

        if ($request->filled('price_max')) {
            $query->where('daily_rate', '<=', $request->price_max);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');

        $allowedSorts = ['name', 'daily_rate', 'seating_capacity', 'year'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $vehicles = $query->paginate(12);

        // Get filter options
        $vehicleTypes = Vehicle::where('status', 'active')
            ->distinct()
            ->pluck('type');

        return view('portal.vehicles.index', compact('vehicles', 'vehicleTypes'));
    }

    /**
     * Show vehicle details
     */
    public function show(Vehicle $vehicle): View
    {
        // Only show active vehicles
        if ($vehicle->status !== 'active') {
            abort(404);
        }

        // Get similar vehicles
        $similarVehicles = Vehicle::where('status', 'active')
            ->where('type', $vehicle->type)
            ->where('id', '!=', $vehicle->id)
            ->limit(4)
            ->get();

        return view('portal.vehicles.show', compact('vehicle', 'similarVehicles'));
    }

    /**
     * Check vehicle availability for specific dates
     */
    public function checkAvailability(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Check if vehicle has any conflicting rentals
        $conflicts = $vehicle->rentals()
            ->whereIn('status', ['confirmed', 'in_progress', 'pending'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_datetime', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_datetime', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_datetime', '<=', $request->start_date)
                            ->where('end_datetime', '>=', $request->end_date);
                    });
            })
            ->exists();

        return response()->json([
            'available' => !$conflicts,
            'vehicle_id' => $vehicle->id,
            'message' => $conflicts
                ? 'This vehicle is not available for the selected dates.'
                : 'This vehicle is available!',
        ]);
    }
}
