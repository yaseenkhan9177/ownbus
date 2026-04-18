<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BookingResource;
use App\Models\Rental;
use App\Models\Vehicle;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use App\Services\RentalPriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    protected $priceCalculator;

    public function __construct(RentalPriceCalculator $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * List user's bookings
     */
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->rentals()
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return BookingResource::collection($bookings)->additional([
            'success' => true,
            'message' => 'Bookings retrieved successfully',
        ]);
    }

    /**
     * Create new booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'with_driver' => 'boolean',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

            // Check availability
            $conflicts = $vehicle->rentals()
                ->whereIn('status', ['confirmed', 'active', 'pending'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']]);
                })
                ->exists();

            if ($conflicts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle not available for selected dates',
                ], 422);
            }

            // Create rental
            $rental = Rental::create([
                'company_id' => $vehicle->company_id ?? 1,
                'vehicle_id' => $vehicle->id,
                'customer_id' => $request->user()->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'pickup_location' => $validated['pickup_location'],
                'dropoff_location' => $validated['dropoff_location'],
                'with_driver' => $validated['with_driver'] ?? false,
                'status' => 'pending',
                'payment_status' => 'pending',
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            // Calculate pricing
            $pricing = $this->priceCalculator->calculate($rental);
            $rental->update([
                'subtotal' => $pricing->subtotal,
                'tax' => $pricing->tax,
                'final_amount' => $pricing->final_amount,
            ]);

            DB::commit();

            // Send notification
            $request->user()->notify(new BookingConfirmed($rental));

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => new BookingResource($rental->load('vehicle')),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get booking details
     */
    public function show(Request $request, $id)
    {
        $booking = $request->user()
            ->rentals()
            ->with('vehicle')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, $id)
    {
        $booking = $request->user()
            ->rentals()
            ->findOrFail($id);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or confirmed bookings can be cancelled',
            ], 422);
        }

        DB::transaction(function () use ($booking, $request) {
            $booking->update(['status' => 'cancelled']);
            $request->user()->notify(new BookingCancelled($booking, 'Cancelled by customer'));
        });

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'data' => new BookingResource($booking->fresh()),
        ]);
    }

    /**
     * Calculate price for booking
     */
    public function calculatePrice(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'with_driver' => 'boolean',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        // Create temporary rental for pricing
        $tempRental = new Rental([
            'vehicle_id' => $vehicle->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'with_driver' => $validated['with_driver'] ?? false,
        ]);

        $pricing = $this->priceCalculator->calculate($tempRental);

        return response()->json([
            'success' => true,
            'data' => [
                'subtotal' => (float) $pricing->subtotal,
                'tax' => (float) $pricing->tax,
                'final_amount' => (float) $pricing->final_amount,
                'metadata' => $pricing->metadata,
            ],
        ]);
    }
}
