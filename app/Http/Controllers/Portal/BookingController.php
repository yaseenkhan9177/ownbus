<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Vehicle;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingCancelled;
use App\Services\RentalPriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    protected $priceCalculator;

    public function __construct(RentalPriceCalculator $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * Show booking form for a vehicle
     */
    public function create(Vehicle $vehicle): View
    {
        if ($vehicle->status !== 'active') {
            abort(404, 'Vehicle not available');
        }

        return view('portal.bookings.create', compact('vehicle'));
    }

    /**
     * Calculate price preview (AJAX)
     */
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:tenant.vehicles,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'with_driver' => 'boolean',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Create temporary rental object for price calculation
        $tempRental = new Rental([
            'vehicle_id' => $vehicle->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'with_driver' => $request->with_driver ?? false,
        ]);

        $breakdown = $this->priceCalculator->calculate($tempRental);

        return response()->json([
            'success' => true,
            'breakdown' => $breakdown->toArray(),
        ]);
    }

    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:tenant.vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'with_driver' => 'boolean',
            'special_requests' => 'nullable|string|max:1000',
            'terms_accepted' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            // Double-check availability
            $conflicts = $vehicle->rentals()
                ->whereIn('status', ['confirmed', 'active', 'pending'])
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                })
                ->exists();

            if ($conflicts) {
                return back()->with('error', 'This vehicle is no longer available for the selected dates.');
            }

            // Create rental
            $rental = Rental::create([
                'vehicle_id' => $vehicle->id,
                'customer_id' => auth()->user()->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'pickup_location' => $request->pickup_location,
                'dropoff_location' => $request->dropoff_location,
                'with_driver' => $request->with_driver ?? false,
                'status' => 'pending', // Pending until payment
                'payment_status' => 'pending',
                'special_requests' => $request->special_requests,
            ]);

            // Calculate pricing
            $breakdown = $this->priceCalculator->calculate($rental);

            $rental->update([
                'final_amount' => $breakdown->final_amount,
                'tax' => $breakdown->tax,
            ]);

            DB::commit();

            // Send booking confirmation notification
            $rental->customer->notify(new BookingConfirmed($rental));
            // TODO: Initiate payment flow

            return redirect()->route('portal.bookings.show', $rental)
                ->with('success', 'Booking created successfully! Please complete payment to confirm.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create booking. Please try again.');
        }
    }

    /**
     * Show booking details
     */
    public function show(Rental $rental): View
    {
        // Ensure customer can only see their own bookings
        if ($rental->customer_id !== auth()->id()) {
            abort(403);
        }

        return view('portal.bookings.show', compact('rental'));
    }

    /**
     * Cancel a booking
     */
    public function cancel(Rental $rental)
    {
        // Ensure customer can only cancel their own bookings
        if ($rental->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($rental->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        DB::transaction(function () use ($rental) {
            $rental->update(['status' => 'cancelled']);

            // Send cancellation notification
            $rental->customer->notify(new BookingCancelled($rental, 'Cancelled by customer'));
        });

        // TODO: Process refund if payment was made
        // TODO: Send cancellation email

        return redirect()->route('portal.dashboard')
            ->with('success', 'Booking cancelled successfully.');
    }
}
