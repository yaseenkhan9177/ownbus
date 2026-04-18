@extends('portal.layout')

@section('title', 'Invoice - Booking #' . $rental->id)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <!-- Invoice Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">INVOICE</h1>
                <p class="text-gray-600">Invoice #{{ str_pad($rental->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p class="text-gray-600">Date: {{ now()->format('F d, Y') }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-blue-600">BusRental Pro</h2>
                <p class="text-sm text-gray-600">Professional Bus Rental Services</p>
                <p class="text-sm text-gray-600">Dubai, UAE</p>
                <p class="text-sm text-gray-600">info@busrentalpro.com</p>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="grid grid-cols-2 gap-8 mb-8 pb-8 border-b border-gray-200">
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Bill To:</h3>
                <p class="text-gray-700">{{ $rental->customer->name }}</p>
                <p class="text-gray-600 text-sm">{{ $rental->customer->email }}</p>
                @if($rental->customer->phone)
                <p class="text-gray-600 text-sm">{{ $rental->customer->phone }}</p>
                @endif
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Booking Details:</h3>
                <p class="text-gray-700">Booking ID: #{{ $rental->id }}</p>
                <p class="text-gray-600 text-sm">Status: <span class="font-semibold text-green-600">{{ ucfirst($rental->status) }}</span></p>
                <p class="text-gray-600 text-sm">Payment: <span class="font-semibold text-green-600">{{ ucfirst($rental->payment_status) }}</span></p>
            </div>
        </div>

        <!-- Rental Details -->
        <div class="mb-8">
            <h3 class="font-semibold text-gray-900 mb-4">Rental Information</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">Vehicle</p>
                        <p class="font-semibold text-gray-900">{{ $rental->vehicle->name }}</p>
                        <p class="text-sm text-gray-600">{{ $rental->vehicle->model }} ({{ $rental->vehicle->year }})</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Capacity</p>
                        <p class="font-semibold text-gray-900">{{ $rental->vehicle->seating_capacity }} Seats</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Pickup</p>
                        <p class="font-semibold text-gray-900">{{ $rental->start_date->format('M d, Y H:i') }}</p>
                        <p class="text-sm text-gray-600">{{ $rental->pickup_location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Return</p>
                        <p class="font-semibold text-gray-900">{{ $rental->end_date->format('M d, Y H:i') }}</p>
                        <p class="text-sm text-gray-600">{{ $rental->dropoff_location }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <table class="w-full mb-8">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">Vehicle Rental</p>
                        <p class="text-sm text-gray-600">
                            {{ $rental->start_date->diffInDays($rental->end_date) + 1 }} day(s)
                            @ AED {{ number_format($rental->vehicle->daily_rate, 2) }}/day
                        </p>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">AED {{ number_format($rental->base_price, 2) }}</td>
                </tr>

                @if($rental->driver_fee > 0)
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">Professional Driver</p>
                        <p class="text-sm text-gray-600">
                            {{ $rental->start_date->diffInDays($rental->end_date) + 1 }} day(s)
                        </p>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">AED {{ number_format($rental->driver_fee, 2) }}</td>
                </tr>
                @endif

                <!-- Total -->
                <tr>
                    <td class="px-4 py-4 text-right">
                        <p class="text-xl font-bold text-gray-900">Total Amount</p>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <p class="text-2xl font-bold text-blue-600">AED {{ number_format($rental->final_amount, 2) }}</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Info -->
        @if($rental->payment_status === 'paid')
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-semibold text-green-900">Payment Received</span>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center text-sm text-gray-600 border-t border-gray-200 pt-6">
            <p class="mb-2">Thank you for choosing BusRental Pro!</p>
            <p>For any queries, contact us at info@busrentalpro.com or +971 XX XXX XXXX</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-8">
            <button onclick="window.print()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-semibold">
                Print Invoice
            </button>
            <a href="{{ route('portal.dashboard') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 text-center px-6 py-3 rounded-md font-semibold">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .max-w-4xl,
        .max-w-4xl * {
            visibility: visible;
        }

        .max-w-4xl {
            position: absolute;
            left: 0;
            top: 0;
        }

        button,
        .flex.gap-4 {
            display: none !important;
        }
    }
</style>
@endpush
@endsection