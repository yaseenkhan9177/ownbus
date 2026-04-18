@extends('layouts.company')

@section('title', 'New Rental — Create')

@section('header_title')
<div class="flex items-center gap-3">
    <a href="{{ route('company.rentals.index') }}" class="flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>
    <div>
        <h1 class="text-base font-black text-slate-900 dark:text-white tracking-tight uppercase leading-none">New Rental</h1>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Create Booking</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto" x-data="rentalForm()">

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="mb-6 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-2">Please fix the following errors</p>
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-[11px] text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1.5">
                        <span class="w-1 h-1 bg-rose-400 rounded-full"></span>{{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('company.rentals.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

            {{-- ===== MAIN FORM COLUMN ===== --}}
            <div class="xl:col-span-2 space-y-5">

                {{-- Section 1: Client & Schedule --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Client & Schedule</h2>
                            <p class="text-[9px] text-slate-400 font-medium uppercase tracking-widest mt-0.5">Customer, type & rental period</p>
                        </div>
                        <div class="ml-auto">
                            <span class="text-[9px] font-black text-cyan-500 bg-cyan-50 dark:bg-cyan-500/10 px-2 py-0.5 rounded-full uppercase tracking-widest">Step 01</span>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Customer --}}
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer <span class="text-rose-400">*</span></label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <select name="customer_id" required class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all appearance-none">
                                    <option value="">Select a customer...</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('customer_id') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                        </div>

                        {{-- Rental Type --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rental Type</label>
                            <select name="rental_type" x-model="rentalType" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all appearance-none">
                                <option value="daily">Daily</option>
                                <option value="hourly">Hourly</option>
                                <option value="monthly">Monthly</option>
                                <option value="distance">Distance Based</option>
                            </select>
                        </div>

                        {{-- Rate Type --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rate Scale</label>
                            <select name="rate_type" x-model="rateType" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all appearance-none">
                                <option value="daily">Per Day</option>
                                <option value="weekly">Per Week</option>
                                <option value="monthly">Per Month</option>
                            </select>
                        </div>

                        {{-- Pickup Time --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pickup Date & Time <span class="text-rose-400">*</span></label>
                            <input type="datetime-local" name="start_date" required value="{{ old('start_date') }}"
                                x-model="pickupTime"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all">
                            @error('start_date') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                        </div>

                        {{-- Dropoff Time --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Return Date & Time <span class="text-rose-400">*</span></label>
                            <input type="datetime-local" name="end_date" required value="{{ old('end_date') }}"
                                x-model="dropoffTime"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all">
                            @error('end_date') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                        </div>

                        {{-- Duration Badge --}}
                        <div class="md:col-span-2" x-show="durationLabel">
                            <div class="flex items-center gap-2 px-4 py-2.5 bg-cyan-50 dark:bg-cyan-500/10 rounded-xl border border-cyan-100 dark:border-cyan-500/20">
                                <svg class="w-3.5 h-3.5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-[11px] font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-wider" x-text="durationLabel"></span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Section 2: Assets & Locations --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1 1h8l1-1z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 8h5l3 5v3h-8V8z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Assets & Logistics</h2>
                            <p class="text-[9px] text-slate-400 font-medium uppercase tracking-widest mt-0.5">Vehicle, driver & locations</p>
                        </div>
                        <div class="ml-auto">
                            <span class="text-[9px] font-black text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded-full uppercase tracking-widest">Step 02</span>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Vehicle --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Assigned Vehicle</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1 1h8l1-1z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 8h5l3 5v3h-8V8z" />
                                    </svg>
                                </div>
                                <select name="vehicle_id" class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all appearance-none">
                                    <option value="">Unassigned (set later)</option>
                                    @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vehicle_number }} — {{ $vehicle->make }} {{ $vehicle->model }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('vehicle_id') <p class="text-[10px] text-rose-500 font-bold">{{ $message }}</p> @enderror
                        </div>

                        {{-- Driver --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Driver</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <select name="driver_id" class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition-all appearance-none">
                                    <option value="">Self-Drive / Unassigned</option>
                                    @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Pickup Location --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pickup Location <span class="text-rose-400">*</span></label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="pickup_location" required value="{{ old('pickup_location') }}"
                                    placeholder="e.g. Dubai Airport Terminal 3"
                                    class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 transition-all">
                            </div>
                        </div>

                        {{-- Dropoff Location --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Return Location</label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                </div>
                                <input type="text" name="dropoff_location" value="{{ old('dropoff_location') }}"
                                    placeholder="e.g. HQ Depot, Sheikh Zayed Rd"
                                    class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 transition-all">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Section 3: Notes --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="w-8 h-8 rounded-xl bg-violet-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Notes & Instructions</h2>
                            <p class="text-[9px] text-slate-400 font-medium uppercase tracking-widest mt-0.5">Optional internal remarks</p>
                        </div>
                        <div class="ml-auto">
                            <span class="text-[9px] font-black text-violet-500 bg-violet-50 dark:bg-violet-500/10 px-2 py-0.5 rounded-full uppercase tracking-widest">Step 03</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <textarea name="notes" rows="4"
                            placeholder="Additional instructions, special requirements, pickup notes..."
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500 resize-none transition-all">{{ old('notes') }}</textarea>
                    </div>
                </div>

            </div>

            {{-- ===== SIDEBAR: PRICING ===== --}}
            <div class="space-y-5 xl:sticky xl:top-6">

                {{-- Pricing Card --}}
                <div class="relative bg-slate-900 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-2xl">
                    {{-- Glow accents --}}
                    <div class="absolute -top-8 -right-8 w-32 h-32 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-violet-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative p-6 space-y-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[10px] font-black text-white/50 uppercase tracking-widest">Pricing Summary</h3>
                            <div class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></div>
                        </div>

                        {{-- Rate Amount --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.2em]">Base Rate (AED) <span class="text-rose-400">*</span></label>
                                <button type="button" @click="optimizePricing()" :disabled="optimizing"
                                    class="text-[9px] font-black text-cyan-400 hover:text-cyan-300 uppercase tracking-widest flex items-center gap-1.5 transition-all disabled:opacity-50 group">
                                    <svg class="w-3 h-3" :class="optimizing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span x-text="optimizing ? 'Analyzing...' : 'AI Optimize'"></span>
                                </button>
                            </div>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm font-black">د.إ</span>
                                <input type="number" name="rate_amount" x-model.number="rateAmount" step="0.01" required min="0"
                                    class="w-full pl-10 pr-4 py-3.5 bg-white/10 border border-white/10 rounded-xl text-xl font-black text-white focus:ring-1 focus:ring-cyan-500 focus:bg-white/15 transition-all placeholder-white/20"
                                    placeholder="0.00">
                            </div>
                        </div>

                        {{-- AI Insights Breakdown --}}
                        <template x-if="pricingBreakdown">
                            <div class="p-3 bg-white/5 border border-white/5 rounded-xl space-y-2">
                                <p class="text-[8px] font-black text-cyan-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    AI Pricing Signals Applied
                                </p>
                                <div class="grid grid-cols-2 gap-2 text-[8px] font-bold text-white/40 uppercase tracking-wider">
                                    <div class="flex justify-between border-b border-white/5 pb-1">
                                        <span>Utilization</span>
                                        <span :class="pricingBreakdown.utilization_multiplier > 1 ? 'text-rose-400' : 'text-emerald-400'" x-text="pricingBreakdown.utilization_multiplier + 'x'"></span>
                                    </div>
                                    <div class="flex justify-between border-b border-white/5 pb-1">
                                        <span>Seasonality</span>
                                        <span :class="pricingBreakdown.season_multiplier > 1 ? 'text-rose-400' : 'text-emerald-400'" x-text="pricingBreakdown.season_multiplier + 'x'"></span>
                                    </div>
                                    <div class="flex justify-between border-b border-white/5 pb-1">
                                        <span>Urgency</span>
                                        <span :class="pricingBreakdown.urgency_multiplier > 1 ? 'text-rose-400' : 'text-emerald-400'" x-text="pricingBreakdown.urgency_multiplier + 'x'"></span>
                                    </div>
                                    <div class="flex justify-between border-b border-white/5 pb-1">
                                        <span>Customer Risk</span>
                                        <span :class="pricingBreakdown.risk_multiplier > 1 ? 'text-rose-400' : 'text-emerald-400'" x-text="pricingBreakdown.risk_multiplier + 'x'"></span>
                                    </div>
                                </div>
                                <p class="text-[8px] text-white/30 italic font-medium pt-1">Dynamic price optimized for maximum branch yield.</p>
                            </div>
                        </template>

                        {{-- Grid: Security & Discount --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.15em]">Deposit</label>
                                <input type="number" name="security_deposit" x-model.number="securityDeposit" step="0.01" min="0"
                                    class="w-full px-3 py-2.5 bg-white/10 border border-white/10 rounded-xl text-xs font-black text-white focus:ring-1 focus:ring-cyan-500 transition-all placeholder-white/20"
                                    placeholder="0.00">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.15em]">Discount</label>
                                <input type="number" name="discount" x-model.number="discount" step="0.01" min="0"
                                    class="w-full px-3 py-2.5 bg-white/10 border border-white/10 rounded-xl text-xs font-black text-white focus:ring-1 focus:ring-cyan-500 transition-all placeholder-white/20"
                                    placeholder="0.00">
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-white/10"></div>

                        {{-- Breakdown --}}
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Subtotal</span>
                                <span class="text-xs font-black text-white" x-text="fmt(subtotal())"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">VAT (5%)</span>
                                <span class="text-xs font-black text-white/70" x-text="fmt(vat())"></span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-white/10">
                                <span class="text-[10px] font-black text-cyan-400 uppercase tracking-widest">Grand Total</span>
                                <span class="text-2xl font-black text-cyan-400" x-text="fmt(total())"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-white/30 uppercase tracking-widest">Security Deposit</span>
                                <span class="text-xs font-black text-white/50" x-text="fmt(securityDeposit)"></span>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full py-4 bg-cyan-500 hover:bg-cyan-400 active:bg-cyan-600 text-slate-900 font-black text-xs uppercase tracking-[0.2em] rounded-xl transition-all hover:scale-[1.02] active:scale-95 shadow-xl shadow-cyan-500/30 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Create Rental
                        </button>
                    </div>
                </div>

                {{-- Info Badge --}}
                <div class="flex items-start gap-3 p-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                        Duplicate vehicle or driver bookings for overlapping periods will be automatically blocked on confirmation.
                    </p>
                </div>

                {{-- Back Link --}}
                <a href="{{ route('company.rentals.index') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Rentals
                </a>

            </div>

        </div>
    </form>
</div>

<script>
    function rentalForm() {
        return {
            rentalType: 'daily',
            rateType: 'daily',
            rateAmount: 0,
            securityDeposit: 0,
            discount: 0,
            pickupTime: '{{ old('start_date') }}',
            dropoffTime: '{{ old('end_date') }}',
            optimizing: false,
            pricingBreakdown: null,

            async optimizePricing() {
                const customerId = document.querySelector('select[name="customer_id"]').value;
                const vehicleId = document.querySelector('select[name="vehicle_id"]').value;

                if (!this.pickupTime || !customerId || !vehicleId) {
                    alert('Please select Customer, Vehicle and Pickup Date first.');
                    return;
                }

                this.optimizing = true;
                try {
                    const response = await fetch('{{ route('company.intelligence.calculate-rate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                customer_id: customerId,
                                vehicle_id: vehicleId,
                                start_date: this.pickupTime
                            })
                        });

                    if (!response.ok) throw new Error('Failed to calculate rate');

                    const data = await response.json();
                    this.rateAmount = data.optimized_rate;
                    this.pricingBreakdown = data.breakdown;
                } catch (e) {
                    console.error(e);
                    alert('Error calculating AI rate. Please set manually.');
                } finally {
                    this.optimizing = false;
                }
            },

            get durationLabel() {
                if (!this.pickupTime || !this.dropoffTime) return '';
                const start = new Date(this.pickupTime);
                const end = new Date(this.dropoffTime);
                if (isNaN(start) || isNaN(end) || end <= start) return '';
                const diffMs = end - start;
                const hours = Math.floor(diffMs / 3600000);
                const days = Math.floor(hours / 24);
                if (days > 0) return `Duration: ${days} day${days > 1 ? 's' : ''} ${hours % 24 > 0 ? `${hours % 24}h` : ''}`;
                return `Duration: ${hours} hour${hours !== 1 ? 's' : ''}`;
            },

            subtotal() {
                return Math.max(0, this.rateAmount - this.discount);
            },
            vat() {
                return this.subtotal() * 0.05;
            },
            total() {
                return this.subtotal() + this.vat();
            },
            fmt(value) {
                return new Intl.NumberFormat('en-AE', {
                    style: 'currency',
                    currency: 'AED',
                    minimumFractionDigits: 2,
                }).format(value || 0);
            }
        }
    }
</script>
@endsection