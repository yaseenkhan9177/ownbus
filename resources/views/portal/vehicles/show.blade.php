@extends('portal.layout')

@section('title', $vehicle->name . ' - Vehicle Details')

@section('content')
<div x-data="vehicleDetail()" class="max-w-6xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('portal.vehicles.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Vehicles
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Vehicle Image & Gallery -->
        <div>
            <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg h-96 flex items-center justify-center mb-4">
                <svg class="w-32 h-32 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <!-- TODO: Add image gallery here -->
        </div>

        <!-- Vehicle Info -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $vehicle->name }}</h1>
            <p class="text-gray-600 text-lg mb-4">{{ $vehicle->model }} ({{ $vehicle->year }})</p>

            <!-- Price -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <div class="text-sm text-gray-600 mb-1">Starting from</div>
                <div class="text-4xl font-bold text-blue-600">AED {{ number_format($vehicle->daily_rate, 2) }}</div>
                <div class="text-sm text-gray-600">per day</div>
            </div>

            <!-- Specifications -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Specifications</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <div>
                            <div class="text-sm text-gray-600">Capacity</div>
                            <div class="font-semibold">{{ $vehicle->seating_capacity }} Seats</div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <div>
                            <div class="text-sm text-gray-600">Type</div>
                            <div class="font-semibold">{{ ucfirst($vehicle->type) }}</div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <div class="text-sm text-gray-600">Status</div>
                            <div class="font-semibold text-green-600">Available</div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <div class="text-sm text-gray-600">Year</div>
                            <div class="font-semibold">{{ $vehicle->year }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Features</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Air Conditioning
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        GPS Navigation
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Comfortable Seats
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Professional Driver
                    </div>
                </div>
            </div>

            <!-- Book Now Button -->
            @auth
            @if(auth()->user()->role === 'customer')
            <a href="{{ route('portal.bookings.create', $vehicle) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-6 py-4 rounded-lg font-semibold text-lg">
                Book This Vehicle Now
            </a>
            @endif
            @else
            <a href="{{ route('portal.login') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-6 py-4 rounded-lg font-semibold text-lg">
                Login to Book
            </a>
            @endauth
        </div>
    </div>

    <!-- Similar Vehicles -->
    @if($similarVehicles->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar Vehicles</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($similarVehicles as $similar)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                <div class="h-32 bg-gradient-to-r from-blue-300 to-blue-500 flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $similar->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ $similar->seating_capacity }} Seats</p>
                    <p class="text-blue-600 font-bold mb-3">AED {{ number_format($similar->daily_rate, 2) }}/day</p>
                    <a href="{{ route('portal.vehicles.show', $similar) }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function vehicleDetail() {
        return {
            // Add Alpine.js logic for availability checking if needed
        };
    }
</script>
@endpush
@endsection