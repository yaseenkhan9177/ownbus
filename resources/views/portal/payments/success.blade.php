@extends('portal.layout')

@section('title', 'Payment Successful')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <!-- Success Icon -->
        <div class="mb-6">
            <svg class="w-20 h-20 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <!-- Success Message -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
        <p class="text-lg text-gray-600 mb-8">Your booking has been confirmed.</p>

        <!-- Booking Details Card -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
            <h3 class="font-semibold text-gray-900 mb-4 text-center">Booking Confirmation</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Booking ID:</span>
                    <span class="font-semibold">#{{ str_pad($rental->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Vehicle:</span>
                    <span class="font-semibold">{{ $rental->vehicle->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pickup:</span>
                    <span class="font-semibold">{{ $rental->start_date->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Return:</span>
                    <span class="font-semibold">{{ $rental->end_date->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-300 pt-3">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="text-xl font-bold text-green-600">AED {{ number_format($rental->final_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8 text-left">
            <h4 class="font-semibold text-blue-900 mb-2">📧 What's Next?</h4>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>✅ Confirmation email sent to {{ auth()->user()->email }}</li>
                <li>✅ Invoice is now available in your dashboard</li>
                <li>📅 We'll send you a reminder 24 hours before pickup</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <a href="{{ route('portal.rentals.invoice', $rental) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-semibold">
                View Invoice
            </a>
            <a href="{{ route('portal.dashboard') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-md font-semibold">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection