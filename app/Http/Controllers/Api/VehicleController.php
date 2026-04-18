<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * List all available vehicles with optional filters
     */
    public function index(Request $request)
    {
        $query = Vehicle::where('status', 'active');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by seating capacity
        if ($request->has('min_capacity')) {
            $query->where('seating_capacity', '>=', $request->min_capacity);
        }

        // Filter by price range
        if ($request->has('max_price')) {
            $query->where('daily_rate', '<=', $request->max_price);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $vehicles = $query->orderBy('created_at', 'desc')->paginate(15);

        return VehicleResource::collection($vehicles)->additional([
            'success' => true,
            'message' => 'Vehicles retrieved successfully',
        ]);
    }

    /**
     * Get vehicle details
     */
    public function show($id)
    {
        $vehicle = Vehicle::where('status', 'active')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new VehicleResource($vehicle),
        ]);
    }

    /**
     * Check vehicle availability for date range
     */
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        // Check for conflicts
        $conflicts = $vehicle->rentals()
            ->whereIn('status', ['confirmed', 'in_progress', 'pending'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_datetime', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_datetime', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_datetime', '<=', $validated['start_date'])
                            ->where('end_datetime', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'available' => !$conflicts,
                'vehicle_id' => $vehicle->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ],
            'message' => $conflicts ? 'Vehicle not available for selected dates' : 'Vehicle available',
        ]);
    }
}
