<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Booking;
use Carbon\Carbon;

class QuoteController extends Controller
{
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'pickup_time' => 'required|date|after:now',
            'dropoff_time' => 'required|date|after:pickup_time',
            'vehicle_type' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $pickup = Carbon::parse($request->pickup_time);
        $dropoff = Carbon::parse($request->dropoff_time);
        $days = $pickup->diffInDays($dropoff) ?: 1; // Minimum 1 day
        $quantity = $request->quantity;

        // Find available vehicles of the requested type
        // A vehicle is available if it has NO bookings that overlap with the requested dates
        $availableVehicles = Vehicle::where('type', $request->vehicle_type)
            ->where('status', 'active') // Matches seeder status
            ->whereDoesntHave('bookings', function ($query) use ($pickup, $dropoff) {
                $query->where(function ($q) use ($pickup, $dropoff) {
                    $q->whereBetween('pickup_time', [$pickup, $dropoff])
                        ->orWhereBetween('dropoff_time', [$pickup, $dropoff])
                        ->orWhere(function ($sq) use ($pickup, $dropoff) {
                            $sq->where('pickup_time', '<', $pickup)
                                ->where('dropoff_time', '>', $dropoff);
                        });
                });
            })
            ->get();

        if ($availableVehicles->count() < $quantity) {
            $count = $availableVehicles->count();
            return back()->with('error', "Sorry! Only {$count} buses remain available for these dates.");
        }

        $vehicle = $availableVehicles->first(); // Detailed info from first available
        $totalPrice = ($vehicle->daily_rate * $days) * $quantity;

        return view('quote.result', [
            'vehicle' => $vehicle,
            'count' => $availableVehicles->count(),
            'pickup' => $pickup,
            'dropoff' => $dropoff,
            'days' => $days,
            'quantity' => $quantity,
            'totalPrice' => $totalPrice,
            'vehicleIds' => $availableVehicles->take($quantity)->pluck('id')->implode(','),
        ]);
    }

    public function storeBooking(Request $request)
    {
        $vehicleIds = explode(',', $request->vehicle_ids);
        $pickup = $request->pickup_time;
        $dropoff = $request->dropoff_time;
        // Total price passed is for ALL buses. We can split it or just store it on first? 
        // Better: Store per-booking price.
        $totalPricePerBus = $request->total_price / count($vehicleIds);

        foreach ($vehicleIds as $id) {
            Booking::create([
                'vehicle_id' => $id,
                'pickup_time' => $pickup,
                'dropoff_time' => $dropoff,
                'total_price' => $totalPricePerBus,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('welcome')->with('success', 'Booking request for ' . count($vehicleIds) . ' bus(es) submitted! Our team will contact you shortly.');
    }
}
