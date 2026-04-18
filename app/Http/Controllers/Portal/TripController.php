<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\TripService;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function __construct(protected TripService $tripService) {}

    /**
     * List all trips for the company.
     */
    public function index(Request $request)
    {
        $query = Trip::with(['rental', 'driver', 'vehicle'])
            ->orderBy('actual_start', 'desc');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('actual_start', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('actual_start', '<=', $request->to_date);
        }

        $trips    = $query->paginate(20)->withQueryString();
        $vehicles = Vehicle::orderBy('vehicle_number')->get(['id', 'vehicle_number', 'name']);
        $drivers  = Driver::orderBy('id')->get();
        $stats    = $this->tripService->getFleetStats();

        return view('portal.trips.index', compact('trips', 'vehicles', 'drivers', 'stats'));
    }

    /**
     * Show a single trip detail.
     */
    public function show(Trip $trip)
    {
        $trip->load(['rental.customer', 'driver.user', 'vehicle']);

        return view('portal.trips.show', compact('trip'));
    }

    /**
     * Cancel a trip (company-side override).
     */
    public function cancel(Request $request, Trip $trip)
    {
        if ($trip->isCompleted()) {
            return back()->with('error', 'Completed trips cannot be cancelled.');
        }

        $this->tripService->cancel($trip, $request->reason ?? 'Cancelled by manager');

        return back()->with('success', 'Trip cancelled successfully.');
    }
}
