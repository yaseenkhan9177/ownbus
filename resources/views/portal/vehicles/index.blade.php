@extends('portal.layout')

@section('title', 'Browse Vehicles - BusRental Pro')

@section('content')
<div x-data="vehicleBrowser()">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Browse Our Fleet</h1>
        <p class="text-gray-600 mt-2">Find the perfect vehicle for your journey</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('portal.vehicles.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Vehicle name, model..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Vehicle Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                <select
                    name="type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    @foreach($vehicleTypes as $type)
                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Capacity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min Capacity</label>
                <input
                    type="number"
                    name="capacity_min"
                    value="{{ request('capacity_min') }}"
                    placeholder="e.g. 20"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Max Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Price/Day</label>
                <input
                    type="number"
                    name="price_max"
                    value="{{ request('price_max') }}"
                    placeholder="e.g. 500"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Buttons -->
            <div class="md:col-span-4 flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Apply Filters
                </button>
                <a href="{{ route('portal.vehicles.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-md font-medium">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Vehicle Grid -->
    @if($vehicles->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($vehicles as $vehicle)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <!-- Vehicle Image Placeholder -->
            <div class="h-48 bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>

            <!-- Vehicle Details -->
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $vehicle->name }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ $vehicle->model }} ({{ $vehicle->year }})</p>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-sm">{{ $vehicle->seating_capacity }} Seats</span>
                    </div>
                    <span class="text-blue-600 font-bold text-lg">AED {{ number_format($vehicle->daily_rate, 2) }}/day</span>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('portal.vehicles.show', $vehicle) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-md font-medium">
                        View Details
                    </a>
                    @auth
                    @if(auth()->user()->role === 'customer')
                    <a href="{{ route('portal.bookings.create', $vehicle) }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-md font-medium">
                        Book Now
                    </a>
                    @endif
                    @endauth
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $vehicles->links() }}
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No vehicles found</h3>
        <p class="text-gray-600">Try adjusting your filters or search criteria.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function vehicleBrowser() {
        return {
            // Add any Alpine.js logic here if needed for dynamic filtering
        };
    }
</script>
@endpush
@endsection