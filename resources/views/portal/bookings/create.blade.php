@extends('portal.layout')

@section('title', 'Book ' . $vehicle->name)

@section('content')
<div x-data="bookingWizard()" class="max-w-4xl mx-auto">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center" :class="step >= 1 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300'">1</div>
                <span class="ml-2 font-medium hidden md:inline">Dates & Location</span>
            </div>
            <div class="flex-1 h-1 mx-4" :class="step >= 2 ? 'bg-blue-600' : 'bg-gray-300'"></div>
            <div class="flex items-center" :class="step >= 2 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300'">2</div>
                <span class="ml-2 font-medium hidden md:inline">Review & Confirm</span>
            </div>
        </div>
    </div>

    <!-- Vehicle Summary -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center">
            <div class="w-24 h-24 bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $vehicle->name }}</h2>
                <p class="text-gray-600">{{ $vehicle->model }} ({{ $vehicle->year }})</p>
                <p class="text-blue-600 font-semibold">AED {{ number_format($vehicle->daily_rate, 2) }}/day</p>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <form @submit.prevent="submitBooking" method="POST" action="{{ route('portal.bookings.store') }}">
        @csrf
        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

        <!-- Step 1: Dates & Location -->
        <div x-show="step === 1" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Rental Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time</label>
                    <input
                        type="datetime-local"
                        name="start_date"
                        x-model="formData.start_date"
                        @change="calculatePrice"
                        required
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date & Time</label>
                    <input
                        type="datetime-local"
                        name="end_date"
                        x-model="formData.end_date"
                        @change="calculatePrice"
                        required
                        :min="formData.start_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Pickup Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Location</label>
                    <input
                        type="text"
                        name="pickup_location"
                        x-model="formData.pickup_location"
                        required
                        placeholder="Enter pickup address"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Dropoff Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dropoff Location</label>
                    <input
                        type="text"
                        name="dropoff_location"
                        x-model="formData.dropoff_location"
                        required
                        placeholder="Enter dropoff address"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- With Driver -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        name="with_driver"
                        x-model="formData.with_driver"
                        @change="calculatePrice"
                        value="1"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Include professional driver (+AED 200/day)</span>
                </label>
            </div>

            <!-- Special Requests -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests (Optional)</label>
                <textarea
                    name="special_requests"
                    x-model="formData.special_requests"
                    rows="3"
                    placeholder="Any special requirements or requests..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Price Preview -->
            <div x-show="priceBreakdown" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-gray-900 mb-3">Price Estimate</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Base Price:</span>
                        <span x-text="'AED ' + (priceBreakdown?.base_price || 0).toFixed(2)"></span>
                    </div>
                    <div x-show="priceBreakdown?.driver_fee" class="flex justify-between">
                        <span>Driver Fee:</span>
                        <span x-text="'AED ' + (priceBreakdown?.driver_fee || 0).toFixed(2)"></span>
                    </div>
                    <div class="border-t border-blue-300 pt-2 flex justify-between font-bold text-base">
                        <span>Total:</span>
                        <span x-text="'AED ' + (priceBreakdown?.final_amount || 0).toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <button
                type="button"
                @click="step = 2"
                :disabled="!formData.start_date || !formData.end_date || !formData.pickup_location || !formData.dropoff_location"
                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-md font-semibold">
                Continue to Review
            </button>
        </div>

        <!-- Step 2: Review & Confirm -->
        <div x-show="step === 2" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Review Your Booking</h3>

            <div class="space-y-4 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Start:</span>
                        <p class="font-semibold" x-text="formData.start_date"></p>
                    </div>
                    <div>
                        <span class="text-gray-600">End:</span>
                        <p class="font-semibold" x-text="formData.end_date"></p>
                    </div>
                    <div>
                        <span class="text-gray-600">Pickup:</span>
                        <p class="font-semibold" x-text="formData.pickup_location"></p>
                    </div>
                    <div>
                        <span class="text-gray-600">Dropoff:</span>
                        <p class="font-semibold" x-text="formData.dropoff_location"></p>
                    </div>
                </div>

                <div x-show="formData.with_driver" class="text-sm">
                    <span class="text-gray-600">Driver:</span>
                    <span class="font-semibold text-green-600">✓ Included</span>
                </div>

                <div x-show="formData.special_requests" class="text-sm">
                    <span class="text-gray-600">Special Requests:</span>
                    <p class="font-semibold" x-text="formData.special_requests"></p>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="mb-6">
                <label class="flex items-start">
                    <input
                        type="checkbox"
                        name="terms_accepted"
                        required
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                    <span class="ml-2 text-sm text-gray-700">
                        I agree to the <a href="#" class="text-blue-600 hover:underline">Terms & Conditions</a> and <a href="#" class="text-blue-600 hover:underline">Cancellation Policy</a>
                    </span>
                </label>
            </div>

            <div class="flex gap-4">
                <button
                    type="button"
                    @click="step = 1"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-md font-semibold">
                    Back
                </button>
                <button
                    type="submit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-semibold">
                    Confirm Booking
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function bookingWizard() {
        return {
            step: 1,
            formData: {
                start_date: '',
                end_date: '',
                pickup_location: '',
                dropoff_location: '',
                with_driver: false,
                special_requests: ''
            },
            priceBreakdown: null,

            async calculatePrice() {
                if (!this.formData.start_date || !this.formData.end_date) return;

                try {
                    const response = await fetch('{{ route("portal.bookings.calculate-price") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            vehicle_id: {
                                {
                                    $vehicle - > id
                                }
                            },
                            start_date: this.formData.start_date,
                            end_date: this.formData.end_date,
                            with_driver: this.formData.with_driver
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.priceBreakdown = data.breakdown;
                    }
                } catch (error) {
                    console.error('Price calculation error:', error);
                }
            },

            submitBooking() {
                // Form will submit normally to server
                return true;
            }
        };
    }
</script>
@endpush
@endsection