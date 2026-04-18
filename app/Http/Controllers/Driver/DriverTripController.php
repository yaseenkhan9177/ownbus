<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Trip;
use App\Models\DriverTripReport;
use App\Services\TripService;
use Illuminate\Http\Request;

class DriverTripController extends Controller
{
    public function __construct(protected TripService $tripService) {}

    public function show(Rental $rental)
    {
        $driverId = session('driver_id');
        abort_if($rental->driver_id !== $driverId, 403, 'Access denied.');

        $rental->load(['vehicle', 'customer', 'invoices']);

        // Load or create the trip record for this rental
        $trip = Trip::where('rental_id', $rental->id)
            ->whereIn('status', [Trip::STATUS_PENDING, Trip::STATUS_IN_PROGRESS])
            ->first();

        return view('driver.trip.show', compact('rental', 'trip'));
    }

    public function start(Request $request, Rental $rental)
    {
        $driverId = session('driver_id');
        abort_if($rental->driver_id !== $driverId, 403);

        if ($rental->status === 'confirmed') {
            $rental->update(['status' => 'active']);

            // Get or create a Trip record and start it
            $trip = $this->tripService->ensureTripForRental($rental);
            $this->tripService->start($trip, [
                'odometer_start' => $request->odometer_start,
                'lat'            => $request->lat,
                'lng'            => $request->lng,
            ]);

            // Legacy DriverTripReport log
            DriverTripReport::create([
                'driver_id'   => $driverId,
                'rental_id'   => $rental->id,
                'company_id'  => $rental->company_id,
                'type'        => DriverTripReport::TYPE_STATUS,
                'status'      => DriverTripReport::STATUS_COMPLETED,
                'notes'       => 'Trip started by driver.',
                'metadata'    => ['event' => 'trip_started', 'trip_id' => $trip->id],
                'reported_at' => now(),
            ]);
        }

        return redirect()->route('driver.trip.show', $rental)
            ->with('success', '🚌 Trip started! Drive safe.');
    }

    public function complete(Request $request, Rental $rental)
    {
        $driverId = session('driver_id');
        abort_if($rental->driver_id !== $driverId, 403);

        $request->validate([
            'odometer_end'     => 'nullable|integer|min:0',
            'notes'            => 'nullable|string|max:500',
            'fuel_used_liters' => 'nullable|numeric|min:0',
            'rating'           => 'nullable|integer|min:1|max:5',
        ]);

        if ($rental->status === 'active') {
            $rental->update(['status' => 'completed']);

            // Complete the Trip record
            $trip = Trip::where('rental_id', $rental->id)
                ->where('status', Trip::STATUS_IN_PROGRESS)
                ->first();

            if ($trip) {
                $this->tripService->complete($trip, [
                    'odometer_end'     => $request->odometer_end,
                    'notes'            => $request->notes,
                    'fuel_used_liters' => $request->fuel_used_liters,
                    'rating'           => $request->rating,
                    'lat'              => $request->lat,
                    'lng'              => $request->lng,
                ]);
            } else {
                // Fallback: still update vehicle odometer
                if ($request->filled('odometer_end') && $rental->vehicle) {
                    $rental->vehicle->update(['current_odometer' => $request->odometer_end]);
                }
            }

            // Legacy DriverTripReport log
            DriverTripReport::create([
                'driver_id'   => $driverId,
                'rental_id'   => $rental->id,
                'company_id'  => $rental->company_id,
                'type'        => DriverTripReport::TYPE_STATUS,
                'status'      => DriverTripReport::STATUS_COMPLETED,
                'notes'       => $request->notes ?? 'Trip completed by driver.',
                'metadata'    => [
                    'event'        => 'trip_completed',
                    'odometer_end' => $request->odometer_end,
                    'trip_id'      => $trip?->id,
                ],
                'reported_at' => now(),
            ]);
        }

        return redirect()->route('driver.dashboard')
            ->with('success', '✅ Trip completed successfully!');
    }
}
