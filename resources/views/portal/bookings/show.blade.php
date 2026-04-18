@extends('portal.layout')

@section('title', 'Booking Confirmation')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Success Message -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8 text-center">
        <svg class="w-16 h-16 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Booking Created Successfully!</h1>
        <p class="text-gray-600">Your booking reference: <span class="font-semibold">#{{ $rental->id }}</span></p>
    </div>

    <!-- Booking Details -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Booking Details</h2>

        <!-- Vehicle Info -->
        <div class="flex items-center pb-6 border-b border-gray-200 mb-6">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">{{ $rental->vehicle->name }}</h3>
                <p class="text-sm text-gray-600">{{ $rental->vehicle->model }} ({{ $rental->vehicle->year }})</p>
                <p class="text-sm text-gray-600">{{ $rental->vehicle->seating_capacity }} Seats</p>
            </div>
        </div>

        <!-- Rental Details -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Pickup Date & Time</p>
                <p class="font-semibold text-gray-900">{{ $rental->start_date->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Return Date & Time</p>
                <p class="font-semibold text-gray-900">{{ $rental->end_date->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Pickup Location</p>
                <p class="font-semibold text-gray-900">{{ $rental->pickup_location }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Dropoff Location</p>
                <p class="font-semibold text-gray-900">{{ $rental->dropoff_location }}</p>
            </div>
        </div>

        @if($rental->with_driver)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-semibold text-blue-900">Professional Driver Included</span>
            </div>
        </div>
        @endif

        @if($rental->special_requests)
        <div class="mb-6">
            <p class="text-sm text-gray-600 mb-1">Special Requests</p>
            <p class="text-gray-900">{{ $rental->special_requests }}</p>
        </div>
        @endif

        <!-- Price Breakdown -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="font-semibold text-gray-900 mb-4">Price Breakdown</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Base Price</span>
                    <span class="font-semibold">AED {{ number_format($rental->base_price, 2) }}</span>
                </div>
                @if($rental->driver_fee > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Driver Fee</span>
                    <span class="font-semibold">AED {{ number_format($rental->driver_fee, 2) }}</span>
                </div>
                @endif
                <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between text-lg">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-bold text-blue-600">AED {{ number_format($rental->final_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    @if($rental->payment_status !== 'paid')
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-yellow-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Payment Required</h3>
                <p class="text-sm text-gray-700 mb-4">
                    Your booking is currently <span class="font-semibold">{{ $rental->status }}</span>.
                    Please complete the payment to confirm your booking.
                </p>
                <a href="{{ route('portal.payments.show', $rental) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold">
                    Pay Now - AED {{ number_format($rental->final_amount, 2) }}
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <div>
                <h3 class="font-semibold text-green-900">Payment Confirmed</h3>
                <p class="text-sm text-green-700">Your booking has been confirmed and paid in full.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Next Steps -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4">Next Steps</h3>
        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
            <li>Complete payment to confirm your booking</li>
            <li>You will receive a confirmation email with your booking details</li>
            <li>Our team will contact you 24 hours before pickup</li>
            <li>Bring a valid ID and driving license (if self-drive)</li>
        </ol>
    </div>

    <!-- Actions -->
    <div class="flex gap-4">
        <a href="{{ route('portal.dashboard') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-6 py-3 rounded-md font-semibold">
            Go to Dashboard
        </a>
        <a href="{{ route('portal.vehicles.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 text-center px-6 py-3 rounded-md font-semibold">
            Browse More Vehicles
        </a>
    </div>
</div>
@endsection