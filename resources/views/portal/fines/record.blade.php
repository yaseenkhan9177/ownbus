@extends('layouts.company')

@section('title', 'Record Vehicle Fine')

@section('header_title')
<div class="flex items-center gap-3">
    <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center">
        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Record Fine</h1>
        <p class="text-sm text-slate-400 mt-1">Track accountability, protect company revenue, and automate accounting.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto" x-data="fineForm()">

    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500">
        <h3 class="font-bold mb-2 text-sm uppercase tracking-wider">Please fix the following errors:</h3>
        <ul class="list-disc pl-5 text-sm space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('company.fines.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" @submit="isSubmitting = true">
        @csrf

        <!-- SECTION 1: Basic Info -->
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        1. Fine Basic Info
                    </h2>
                </div>
            </div>

            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fine Number -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Fine / Reference No.</label>
                    <input type="text" name="fine_number" value="{{ old('fine_number', 'FN-'.strtoupper(Str::random(8))) }}" class="w-full h-11 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <!-- Date -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Fine Date</label>
                    <input type="date" name="fine_date" value="{{ old('fine_date', date('Y-m-d')) }}" class="w-full h-11 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <!-- Fine Type -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Fine Type</label>
                    <select name="fine_type" x-model="fineType" class="w-full h-11 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                        <option value="">Select Type...</option>
                        <option value="Traffic Violation" class="text-orange-500 font-bold">Traffic Violation</option>
                        <option value="Vehicle Damage" class="text-red-500 font-bold">Vehicle Damage</option>
                        <option value="Late Return" class="text-yellow-500 font-bold">Late Return</option>
                        <option value="Fuel Shortage" class="text-purple-500 font-bold">Fuel Shortage</option>
                        <option value="Other" class="text-blue-500 font-bold">Other</option>
                    </select>
                </div>

                <!-- Rental / Contract -->
                <div class="space-y-2 md:col-span-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Related Contract (Optional)</label>
                    <select name="rental_id" id="rentalSelect" x-model="selectedRental" @change="loadRentalDefaults" class="w-full h-11 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">No specific contract (General Fine)</option>
                        @foreach($rentals as $rental)
                        <option value="{{ $rental->id }}"
                            data-vehicle="{{ $rental->vehicle_id }}"
                            data-driver="{{ $rental->driver_id }}"
                            data-customer="{{ $rental->customer_id }}">
                            {{ $rental->contract_no ?? $rental->rental_number }} - {{ $rental->customer->name ?? 'Unknown Customer' }} ({{ $rental->start_date->format('M d') }} - {{ $rental->end_date->format('M d') }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Selecting a contract will auto-fill the vehicle, driver, and customer below.</p>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Assign Responsibility -->
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden" :class="{ 'border-red-500/30 shadow-[0_0_15px_rgba(239,68,68,0.1)]': fineType === 'Vehicle Damage', 'border-orange-500/30 shadow-[0_0_15px_rgba(249,115,22,0.1)]': fineType === 'Traffic Violation' }">
            <div class="px-8 py-5 border-b border-gray-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full transition-colors duration-300"
                        :class="{
                              'bg-red-500': fineType === 'Vehicle Damage',
                              'bg-orange-500': fineType === 'Traffic Violation',
                              'bg-yellow-500': fineType === 'Late Return',
                              'bg-blue-500': fineType === 'Other' || !fineType
                          }"></span>
                    2. Assign Responsibility
                </h2>
            </div>

            <div class="p-8 space-y-8">
                <!-- Responsibility Radios -->
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-4">Who is accountable for this fine?</label>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <label class="relative flex cursor-pointer rounded-2xl border bg-white dark:bg-slate-950 p-4 transition-all hover:bg-slate-50 dark:hover:bg-slate-800"
                            :class="responsibility === 'driver' ? 'border-blue-500 ring-1 ring-blue-500 shadow-sm' : 'border-gray-200 dark:border-slate-800'">
                            <input type="radio" name="responsible_type" value="driver" x-model="responsibility" class="sr-only">
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-gray-900 dark:text-white">Driver</span>
                                <span class="text-xs text-slate-500 mt-1">Deduct from salary</span>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-2xl border bg-white dark:bg-slate-950 p-4 transition-all hover:bg-slate-50 dark:hover:bg-slate-800"
                            :class="responsibility === 'customer' ? 'border-blue-500 ring-1 ring-blue-500 shadow-sm' : 'border-gray-200 dark:border-slate-800'">
                            <input type="radio" name="responsible_type" value="customer" x-model="responsibility" class="sr-only">
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-gray-900 dark:text-white">Customer</span>
                                <span class="text-xs text-slate-500 mt-1">Add to invoice</span>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-2xl border bg-white dark:bg-slate-950 p-4 transition-all hover:bg-slate-50 dark:hover:bg-slate-800"
                            :class="responsibility === 'both' ? 'border-purple-500 ring-1 ring-purple-500 shadow-sm' : 'border-gray-200 dark:border-slate-800'">
                            <input type="radio" name="responsible_type" value="both" x-model="responsibility" class="sr-only">
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-purple-600 dark:text-purple-400">Both</span>
                                <span class="text-xs text-slate-500 mt-1">Split 50/50</span>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-2xl border bg-white dark:bg-slate-950 p-4 transition-all hover:bg-slate-50 dark:hover:bg-slate-800"
                            :class="responsibility === 'company' ? 'border-rose-500 ring-1 ring-rose-500 shadow-sm' : 'border-gray-200 dark:border-slate-800'">
                            <input type="radio" name="responsible_type" value="company" x-model="responsibility" class="sr-only">
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-rose-600 dark:text-rose-400">Company (Internal)</span>
                                <span class="text-xs text-slate-500 mt-1">Absorb as expense</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Stakeholder Dropdowns -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/30 border border-gray-100 dark:border-slate-800">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Involved Vehicle <span class="text-red-500">*</span></label>
                        <select name="vehicle_id" id="vehicle_id" class="w-full h-11 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }} - {{ $vehicle->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2" x-show="responsibility === 'driver' || responsibility === 'both' || responsibility === 'company'" x-transition>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Involved Driver</label>
                        <select name="driver_id" id="driver_id" class="w-full h-11 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select Driver (Optional if unknown)</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->license_number ?? 'No License' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2" x-show="responsibility === 'customer' || responsibility === 'both'" x-transition>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Involved Customer</label>
                        <select name="customer_id" id="customer_id" class="w-full h-11 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 3: Fine Details -->
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    3. Fine Details
                </h2>
            </div>

            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Amount -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Fine Amount (AED) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <span class="text-slate-400 font-medium">AED</span>
                        </div>
                        <input type="number" step="0.01" min="0.01" name="amount" x-model="amount" class="w-full h-12 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl pl-14 pr-4 text-lg font-bold focus:ring-2 focus:ring-blue-500 outline-none" required placeholder="0.00">
                    </div>
                </div>

                <!-- Due Date -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Payment Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full h-12 bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl px-4 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Description -->
                <div class="space-y-2 md:col-span-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Description / Violation Details</label>
                    <textarea name="description" rows="3" class="w-full bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-xl p-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Provide details about the incident..."></textarea>
                </div>

                <!-- Attachment -->
                <div class="space-y-2 md:col-span-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Upload Proof (Challan / Photo / Receipt)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-2xl cursor-pointer bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-2 text-sm text-slate-500 dark:text-slate-400"><span class="font-bold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">PDF, JPG, PNG (MAX. 5MB)</p>
                            </div>
                            <input type="file" name="attachment" class="hidden" />
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Payment Section -->
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden" :class="paymentStatus === 'paid' ? 'border-emerald-500/50 shadow-[0_0_20px_rgba(16,185,129,0.1)]' : ''">
            <div class="px-8 py-5 border-b border-gray-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="text-xs font-black tracking-widest flex items-center gap-2" :class="paymentStatus === 'paid' ? 'text-emerald-500' : 'text-slate-400'">
                    <span class="w-2 h-2 rounded-full transition-colors duration-300" :class="paymentStatus === 'paid' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                    4. Initial Payment Status (RTA / Authority)
                </h2>

                <div class="flex items-center gap-2 bg-white dark:bg-slate-950 p-1 rounded-xl border border-gray-200 dark:border-slate-800">
                    <button type="button" @click="paymentStatus = 'pending'" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all" :class="paymentStatus === 'pending' ? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
                        Pending / Unpaid
                    </button>
                    <button type="button" @click="paymentStatus = 'paid'" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all" :class="paymentStatus === 'paid' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
                        Paid Immediately
                    </button>
                    <input type="hidden" name="payment_status" x-model="paymentStatus">
                </div>
            </div>

            <div x-show="paymentStatus === 'paid'" x-collapse x-cloak>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 bg-emerald-50/30 dark:bg-emerald-500/5 border-b border-emerald-100 dark:border-emerald-500/10">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">How was it paid?</label>
                        <select name="payment_method" class="w-full h-11 bg-white dark:bg-slate-950 border border-emerald-200 dark:border-emerald-500/30 rounded-xl px-4 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" :required="paymentStatus === 'paid'">
                            <option value="">Select Method...</option>
                            <option value="cash">Company Cash</option>
                            <option value="bank">Bank Transfer / Card</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Company Payment Warning</label>
                        <div class="h-11 flex items-center px-4 rounded-xl bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 text-orange-700 dark:text-orange-400 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            This will record an expense parameter immediately. Recovery will be handled separately based on responsibility.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('company.fines.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/30 transition flex items-center gap-2" :class="{ 'opacity-70 cursor-not-allowed': isSubmitting }">
                <span x-show="!isSubmitting">Record Fine & Apply Logic</span>
                <span x-show="isSubmitting">Processing...</span>
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('fineForm', () => ({
            fineType: '{{ old("fine_type", "") }}',
            selectedRental: '{{ old("rental_id", "") }}',
            responsibility: '{{ old("responsible_type", "driver") }}',
            amount: '{{ old("amount", "") }}',
            paymentStatus: '{{ old("payment_status", "pending") }}',
            isSubmitting: false,

            loadRentalDefaults() {
                if (!this.selectedRental) return;

                const select = document.getElementById('rentalSelect');
                const option = select.options[select.selectedIndex];

                if (option && option.value) {
                    const vehicleId = option.dataset.vehicle;
                    const driverId = option.dataset.driver;
                    const customerId = option.dataset.customer;

                    if (vehicleId) document.getElementById('vehicle_id').value = vehicleId;
                    if (driverId) document.getElementById('driver_id').value = driverId;
                    if (customerId) document.getElementById('customer_id').value = customerId;
                }
            }
        }))
    })
</script>
@endpush
@endsection