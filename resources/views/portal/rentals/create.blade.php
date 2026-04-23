@extends('layouts.company')

@section('title', 'New Rental — Enterprise Booking')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --color-teal: #00BCD4;
        --color-teal-glow: rgba(0, 188, 212, 0.4);
        --color-gold: #F59E0B;
        --bg-root: #0A0F1E;
        --bg-card: #111827;
        --border-card: #1F2937;
    }
    body, .rental-root { font-family: 'DM Sans', sans-serif; background-color: var(--bg-root); color: #F9FAFB; }
    
    /* Glass Cards */
    .premium-card {
        background: rgba(17, 24, 39, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-card);
        border-radius: 1.25rem;
        transition: all 0.3s ease;
    }
    
    /* Toggle Selectable Cards */
    .toggle-card {
        background: rgba(31, 41, 55, 0.5);
        border: 1px solid var(--border-card);
        border-radius: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .toggle-card:hover { background: rgba(31, 41, 55, 0.9); }
    .toggle-card.selected {
        border-color: var(--color-teal);
        background: rgba(0, 188, 212, 0.05);
        box-shadow: 0 0 15px var(--color-teal-glow);
    }
    .toggle-card.disabled {
        opacity: 0.4; pointer-events: none; filter: grayscale(100%);
    }

    /* Form Inputs */
    .premium-input {
        width: 100%;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid var(--border-card);
        color: white;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .premium-input:focus { border-color: var(--color-teal); outline: none; box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.15); }
    .premium-input::placeholder { color: #6B7280; }
    select.premium-input option { background: var(--bg-card); color: white; }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg-root); }
    ::-webkit-scrollbar-thumb { background: #374151; border-radius: 3px; }

    /* Animations */
    .shimmer-btn { position: relative; overflow: hidden; }
    .shimmer-btn::after {
        content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 2.5s infinite;
    }
    @keyframes shimmer { 100% { left: 200%; } }
    
    .pulse-dot { animation: pulse 2s infinite; }
    @keyframes pulse { 
        0% { box-shadow: 0 0 0 0 var(--color-teal-glow); } 
        70% { box-shadow: 0 0 0 8px rgba(0,188,212,0); } 
        100% { box-shadow: 0 0 0 0 rgba(0,188,212,0); } 
    }
    
    /* Progress Indicator */
    .step-node {
        transition: all 0.3s ease;
    }
    .step-node.active {
        background: var(--color-teal);
        box-shadow: 0 0 15px var(--color-teal-glow);
        color: #000;
        border-color: var(--color-teal);
    }
    .step-node.completed {
        background: rgba(0, 188, 212, 0.2);
        color: var(--color-teal);
        border-color: var(--color-teal);
    }
    .step-line { transition: all 0.3s ease; }
    .step-line.completed { background: var(--color-teal); }

    [x-cloak] { display: none !important; }
</style>
@endpush

@section('header_title')
<div class="flex items-center gap-4">
    <a href="{{ route('company.rentals.index') }}" class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-700 hover:text-white transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <div class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 rounded-full bg-[#00BCD4] pulse-dot"></div>
            <h1 class="text-2xl font-black text-white tracking-widest uppercase">NEW RENTAL</h1>
        </div>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Dashboard &rarr; Rentals &rarr; Create Booking</p>
    </div>
</div>
@endsection

@section('content')
<div class="rental-root pb-20" x-data="rentalBooking()" @keydown.window.ctrl.enter="submit" @keydown.window.escape="window.location='{{ route('company.rentals.index') }}'">
    
    {{-- Validation Errors (Laravel Fallback) --}}
    @if($errors->any())
    <div class="mb-6 premium-card border-red-500/30 bg-red-900/10 p-5">
        <p class="text-xs font-black text-red-400 uppercase tracking-widest mb-2">Please fix the following errors</p>
        <ul class="space-y-1">
            @foreach($errors->all() as $error)
            <li class="text-[11px] text-red-300 font-medium flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>{{ $error }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Progress Steps --}}
    <div class="mb-8 premium-card p-5 overflow-x-auto">
        <div class="flex items-center justify-between min-w-[600px] relative">
            <div class="absolute left-10 right-10 top-1/2 -translate-y-1/2 h-1 bg-slate-800 rounded-full -z-10"></div>
            <div class="absolute left-10 top-1/2 -translate-y-1/2 h-1 step-line rounded-full -z-10" :style="`width: ${Math.max(0, (step - 1) * 33.33)}%`"></div>
            
            <template x-for="(s, index) in ['Client', 'Assets', 'Options', 'Confirm']">
                <div class="flex flex-col items-center gap-2 cursor-pointer" @click="step = index + 1">
                    <div class="w-10 h-10 rounded-full border-2 border-slate-700 bg-[#0A0F1E] flex items-center justify-center font-black text-sm step-node"
                         :class="{ 'active': step === index + 1, 'completed': step > index + 1 }">
                        <span x-text="index + 1"></span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest" :class="step >= index + 1 ? 'text-white' : 'text-slate-500'" x-text="s"></span>
                </div>
            </template>
        </div>
    </div>

    <form id="rentalForm" action="{{ route('company.rentals.store') }}" @submit.prevent="submit">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
            
            {{-- ===== MAIN FORM COLUMN ===== --}}
            <div class="xl:col-span-2 relative">

                {{-- STEP 01: CLIENT & SCHEDULE --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    <div class="premium-card p-6 border-[#00BCD4]/30">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
                            <h2 class="text-sm font-black text-[#00BCD4] uppercase tracking-widest">01. Client & Schedule</h2>
                        </div>

                        {{-- Customer Selector --}}
                        <div class="space-y-4 mb-8">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Customer Selection <span class="text-red-400">*</span></label>
                            
                            <div class="relative">
                                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <select name="customer_id" x-model="customerId" required class="premium-input pl-12 h-14 text-base appearance-none cursor-pointer">
                                    <option value="">Search or select a customer...</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Selected Customer Info Card --}}
                            <div x-show="selectedCustomer" x-collapse>
                                <div class="mt-3 p-4 rounded-xl bg-slate-800/50 border border-slate-700 flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center text-lg font-black text-[#00BCD4] shrink-0" x-text="selectedCustomer ? selectedCustomer.name.charAt(0) : ''"></div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-white text-base truncate" x-text="selectedCustomer ? selectedCustomer.name : ''"></h4>
                                        <div class="flex items-center gap-4 mt-1 text-[11px] text-slate-400">
                                            <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> <span x-text="selectedCustomer ? selectedCustomer.phone : ''"></span></span>
                                            <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> <span x-text="selectedCustomer ? selectedCustomer.email : ''"></span></span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-[#00BCD4]/10 text-[#00BCD4] text-[9px] font-black uppercase tracking-widest">
                                            ⭐ Premium Client
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Rental Type --}}
                        <div class="mb-8">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Rental Protocol</label>
                            <input type="hidden" name="rental_type" x-model="rentalType">
                            <input type="hidden" name="rate_type" x-model="rateType">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <template x-for="type in [{id:'daily', icon:'📅', label:'Daily'}, {id:'hourly', icon:'⏱️', label:'Hourly'}, {id:'monthly', icon:'🗓️', label:'Monthly'}, {id:'distance', icon:'🗺️', label:'Distance'}]">
                                    <div class="toggle-card p-4 text-center" :class="{ 'selected': rentalType === type.id }" @click="rentalType = type.id; rateType = (type.id==='distance'?'daily':(type.id==='hourly'?'daily':type.id));">
                                        <div class="text-2xl mb-2" x-text="type.icon"></div>
                                        <p class="text-xs font-black text-white uppercase tracking-widest" x-text="type.label"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Date & Time --}}
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Flight Plan (Schedule) <span class="text-red-400">*</span></label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 bg-[#111827] px-1 text-[9px] font-black text-slate-400 uppercase tracking-widest z-10">Departure</label>
                                    <input type="datetime-local" name="start_date" x-model="pickupTime" required class="premium-input pt-4">
                                </div>
                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 bg-[#111827] px-1 text-[9px] font-black text-slate-400 uppercase tracking-widest z-10">Arrival / Return</label>
                                    <input type="datetime-local" name="end_date" x-model="dropoffTime" required class="premium-input pt-4">
                                </div>
                            </div>
                            
                            {{-- Duration Badge --}}
                            <div x-show="durationLabel" class="mt-4 flex items-center justify-between p-3 rounded-lg bg-[#00BCD4]/10 border border-[#00BCD4]/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#00BCD4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-black text-[#00BCD4] uppercase tracking-widest">Total Duration</span>
                                </div>
                                <span class="text-sm font-black text-white" x-text="durationLabel"></span>
                            </div>
                        </div>

                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="step = 2" class="px-6 py-3 bg-white hover:bg-slate-200 text-black text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg">Next: Assets &rarr;</button>
                    </div>
                </div>

                {{-- STEP 02: ASSETS & LOGISTICS --}}
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
                    <div class="premium-card p-6 border-[#F59E0B]/30">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
                            <h2 class="text-sm font-black text-[#F59E0B] uppercase tracking-widest">02. Assets & Logistics</h2>
                        </div>

                        {{-- Vehicle Grid --}}
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Select Craft (Vehicle) <span class="text-red-400">*</span></label>
                                <span class="text-[9px] text-[#00BCD4] uppercase font-bold" x-show="pickupTime">Filtered by availability</span>
                            </div>
                            <input type="hidden" name="vehicle_id" x-model="vehicleId" required>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($vehicles as $vehicle)
                                <div class="toggle-card p-4 relative overflow-hidden group" :class="{ 'selected': vehicleId === '{{ $vehicle->id }}' }" @click="vehicleId = '{{ $vehicle->id }}'; updateRate({{ $vehicle->id }})">
                                    <div class="flex gap-4">
                                        <div class="w-16 h-16 rounded-lg bg-slate-800 border border-slate-700 shrink-0 flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">
                                            🚌
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-black text-white text-sm uppercase truncate">{{ $vehicle->vehicle_number }}</h4>
                                            <p class="text-[10px] text-slate-400 font-medium truncate">{{ $vehicle->make }} {{ $vehicle->model }} • {{ $vehicle->seating_capacity }} Seats</p>
                                            <div class="mt-2 flex items-center justify-between">
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-emerald-400 uppercase bg-emerald-400/10 px-1.5 py-0.5 rounded"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Available</span>
                                                <span class="text-xs font-black text-[#F59E0B]">AED {{ number_format($vehicle->daily_rate ?? 500, 0) }}/d</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-white/10 rounded-xl pointer-events-none transition-colors"></div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Driver Grid --}}
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Captain (Driver)</label>
                                <button type="button" @click="driverId = ''" class="text-[9px] text-slate-400 hover:text-white uppercase font-bold transition-colors">Clear / Self-Drive</button>
                            </div>
                            <input type="hidden" name="driver_id" x-model="driverId">
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div class="toggle-card p-3 text-center flex flex-col justify-center" :class="{ 'selected': driverId === '' }" @click="driverId = ''">
                                    <span class="text-2xl mb-1">🚗</span>
                                    <span class="text-[10px] font-black text-white uppercase">Self Drive</span>
                                </div>
                                @foreach($drivers as $driver)
                                <div class="toggle-card p-3 flex items-center gap-3" :class="{ 'selected': driverId === '{{ $driver->id }}' }" @click="driverId = '{{ $driver->id }}'">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs font-black text-white shrink-0">
                                        {{ substr($driver->name, 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-white truncate">{{ $driver->name }}</p>
                                        <p class="text-[9px] text-emerald-400 uppercase font-bold tracking-widest">⭐ {{ number_format(rand(40,50)/10, 1) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Locations --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Terminal 1 (Pickup) <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <input type="text" name="pickup_location" x-model="pickupLocation" required class="premium-input pl-10" placeholder="Location...">
                                </div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <template x-for="loc in ['DXB Airport', 'Abu Dhabi', 'Marina']">
                                        <button type="button" @click="pickupLocation = loc" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold uppercase transition-colors" x-text="loc"></button>
                                    </template>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Terminal 2 (Dropoff)</label>
                                <div class="relative">
                                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    <input type="text" name="dropoff_location" x-model="dropoffLocation" class="premium-input pl-10" placeholder="Same as pickup...">
                                </div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <button type="button" @click="dropoffLocation = pickupLocation" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold uppercase transition-colors">Same as Pickup</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="flex justify-between">
                        <button type="button" @click="step = 1" class="px-6 py-3 bg-transparent hover:bg-slate-800 border border-slate-700 text-slate-300 text-xs font-black uppercase tracking-widest rounded-xl transition-all">&larr; Back</button>
                        <button type="button" @click="step = 3" class="px-6 py-3 bg-white hover:bg-slate-200 text-black text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg">Next: Options &rarr;</button>
                    </div>
                </div>

                {{-- STEP 03: ADDITIONAL OPTIONS --}}
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
                    <div class="premium-card p-6 border-purple-500/30">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
                            <h2 class="text-sm font-black text-purple-400 uppercase tracking-widest">03. Additional Options</h2>
                        </div>

                        {{-- Extras Checkboxes --}}
                        <div class="mb-8">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Extras & Upgrades</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-700 bg-slate-800/50 cursor-pointer hover:bg-slate-800 transition-colors">
                                    <input type="checkbox" x-model="options.overtime" class="w-4 h-4 rounded border-slate-600 text-purple-500 focus:ring-purple-500 bg-slate-900">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-white uppercase tracking-wider">Driver Overtime</p>
                                        <p class="text-[9px] text-slate-400 font-bold">AED 50/hr after 10 hours</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-700 bg-slate-800/50 cursor-pointer hover:bg-slate-800 transition-colors">
                                    <input type="checkbox" x-model="options.gps" class="w-4 h-4 rounded border-slate-600 text-purple-500 focus:ring-purple-500 bg-slate-900">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-white uppercase tracking-wider">GPS Fleet Tracking</p>
                                        <p class="text-[9px] text-slate-400 font-bold">Live sharing link included</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-700 bg-slate-800/50 cursor-pointer hover:bg-slate-800 transition-colors">
                                    <input type="checkbox" x-model="options.childSeat" class="w-4 h-4 rounded border-slate-600 text-purple-500 focus:ring-purple-500 bg-slate-900">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-white uppercase tracking-wider">Child Seat</p>
                                        <p class="text-[9px] text-slate-400 font-bold">AED 25/day flat rate</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-700 bg-slate-800/50 cursor-pointer hover:bg-slate-800 transition-colors">
                                    <input type="checkbox" x-model="options.insurance" class="w-4 h-4 rounded border-slate-600 text-purple-500 focus:ring-purple-500 bg-slate-900">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-white uppercase tracking-wider">Full Coverage Insurance</p>
                                        <p class="text-[9px] text-slate-400 font-bold">Zero deductible</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Special Instructions --}}
                        <div class="mb-8">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Special Manifest (Instructions)</label>
                            <textarea name="notes" x-model="notes" rows="3" class="premium-input resize-none" placeholder="Enter VIP requirements, specific routes, or flight numbers..."></textarea>
                        </div>

                        {{-- Generate Agreement --}}
                        <div class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20">
                            <label class="flex items-center gap-3 cursor-pointer mb-3">
                                <input type="checkbox" x-model="generateAgreement" class="w-4 h-4 rounded border-purple-500/50 text-purple-500 focus:ring-purple-500 bg-slate-900">
                                <span class="text-xs font-black text-purple-300 uppercase tracking-widest">Auto-Generate Legal Agreement</span>
                            </label>
                            <div x-show="generateAgreement" x-collapse>
                                <select x-model="agreementTemplate" class="premium-input bg-slate-900 w-1/2">
                                    <option value="standard">Standard Rental Agreement (UAE)</option>
                                    <option value="corporate">Corporate Transport SLA</option>
                                    <option value="tourism">Tourism / Event Contract</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="flex justify-between">
                        <button type="button" @click="step = 2" class="px-6 py-3 bg-transparent hover:bg-slate-800 border border-slate-700 text-slate-300 text-xs font-black uppercase tracking-widest rounded-xl transition-all">&larr; Back</button>
                        <button type="button" @click="step = 4" class="px-6 py-3 bg-white hover:bg-slate-200 text-black text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg">Review & Confirm &rarr;</button>
                    </div>
                </div>

                {{-- STEP 04: CONFIRM --}}
                <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
                    <div class="premium-card p-8 border-emerald-500/30 text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-emerald-500/5 pointer-events-none"></div>
                        <div class="w-20 h-20 mx-auto bg-emerald-500/20 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-black text-white uppercase tracking-widest mb-2">Ready for Takeoff</h2>
                        <p class="text-sm text-slate-400 mb-8">Review the pricing summary on the right and confirm to secure this booking.</p>
                        
                        <div class="flex flex-col gap-3 max-w-sm mx-auto">
                            <button type="submit" :disabled="submitting" class="w-full py-4 bg-[#00BCD4] hover:bg-[#00a0b5] disabled:bg-slate-700 disabled:text-slate-500 text-slate-900 font-black text-sm uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-[#00BCD4]/30 flex items-center justify-center gap-3">
                                <span x-show="submitting" class="w-5 h-5 border-2 border-slate-900/30 border-t-slate-900 rounded-full animate-spin"></span>
                                <span x-text="submitting ? 'PROCESSING...' : 'CONFIRM RENTAL BOOKING'"></span>
                            </button>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest">Or press Ctrl + Enter</p>
                        </div>
                    </div>
                    <div class="flex justify-start">
                        <button type="button" @click="step = 3" class="px-6 py-3 bg-transparent hover:bg-slate-800 border border-slate-700 text-slate-300 text-xs font-black uppercase tracking-widest rounded-xl transition-all">&larr; Back to Options</button>
                    </div>
                </div>

            </div>

            {{-- ===== SIDEBAR: PRICING ===== --}}
            <div class="space-y-5 xl:sticky xl:top-6">
                <div class="premium-card relative overflow-hidden shadow-2xl">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#00BCD4]/20 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-4">
                            <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2"><svg class="w-4 h-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Pricing Summary</h3>
                            <span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-400 text-[9px] font-black uppercase tracking-widest border border-purple-500/20">🤖 AI Active</span>
                        </div>

                        {{-- Dynamic Preview --}}
                        <div class="space-y-2 mb-6">
                            <p class="text-xs font-bold text-white truncate" x-text="selectedVehicle ? (selectedVehicle.make + ' ' + selectedVehicle.model) : 'No Vehicle Selected'"></p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest" x-text="durationLabel ? durationLabel : 'No Dates Selected'"></p>
                        </div>

                        {{-- Editable Fields --}}
                        <div class="space-y-4 mb-6 p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <div>
                                <div class="flex justify-between mb-1.5">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Base Rate (AED)</label>
                                    <button type="button" @click="optimizePricing" class="text-[9px] font-black text-[#00BCD4] hover:text-white uppercase tracking-widest transition-colors flex items-center gap-1"><span x-show="optimizing" class="w-2 h-2 rounded-full border-2 border-[#00BCD4] border-t-transparent animate-spin"></span> AI Optimize</button>
                                </div>
                                <input type="number" name="rate_amount" x-model.number="baseRateAmount" required class="premium-input bg-slate-950 h-10 text-right font-mono" placeholder="0.00">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Deposit</label>
                                    <input type="number" name="security_deposit" x-model.number="securityDeposit" class="premium-input bg-slate-950 h-10 text-right font-mono" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Discount</label>
                                    <input type="number" name="discount" x-model.number="discount" class="premium-input bg-slate-950 h-10 text-right font-mono text-[#F59E0B]" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        {{-- Breakdown --}}
                        <div class="space-y-3 font-mono text-[11px]">
                            <div class="flex justify-between text-slate-300">
                                <span class="uppercase tracking-widest">Base Rate</span>
                                <span x-text="fmt(baseRateAmount)"></span>
                            </div>
                            <div class="flex justify-between text-slate-300" x-show="extrasTotal > 0">
                                <span class="uppercase tracking-widest">Extras</span>
                                <span x-text="fmt(extrasTotal)"></span>
                            </div>
                            <div class="flex justify-between text-[#F59E0B]" x-show="discount > 0">
                                <span class="uppercase tracking-widest">Discount</span>
                                <span x-text="'- ' + fmt(discount)"></span>
                            </div>
                            <div class="border-t border-slate-700 pt-3 flex justify-between text-white font-bold">
                                <span class="uppercase tracking-widest">Subtotal</span>
                                <span x-text="fmt(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span class="uppercase tracking-widest">VAT (5%)</span>
                                <span x-text="fmt(vat)"></span>
                            </div>
                            <div class="border-t border-slate-700 pt-3 flex justify-between text-[#00BCD4] text-sm font-black">
                                <span class="uppercase tracking-widest">Grand Total</span>
                                <span x-text="fmt(total)"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>

    {{-- SUCCESS MODAL --}}
    <div x-show="successData" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" x-transition:enter="transition opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>
        <div class="premium-card p-8 w-full max-w-md relative z-10 border-[#00BCD4]" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-90 translate-y-8" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="w-16 h-16 mx-auto bg-[#00BCD4]/20 rounded-full flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-[#00BCD4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-xl font-black text-white uppercase tracking-widest text-center mb-6">Rental Created Successfully!</h2>
            
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 mb-6 space-y-2 text-xs font-bold text-slate-300 font-mono">
                <div class="flex justify-between"><span class="text-slate-500 uppercase tracking-widest">Rental #</span> <span class="text-[#00BCD4]" x-text="successData?.rental?.rental_number"></span></div>
                <div class="flex justify-between"><span class="text-slate-500 uppercase tracking-widest">Customer</span> <span class="text-white" x-text="successData?.rental?.customer?.name"></span></div>
                <div class="flex justify-between"><span class="text-slate-500 uppercase tracking-widest">Vehicle</span> <span class="text-white" x-text="successData?.rental?.vehicle?.vehicle_number || 'N/A'"></span></div>
                <div class="flex justify-between"><span class="text-slate-500 uppercase tracking-widest">Total</span> <span class="text-[#F59E0B]" x-text="fmt(successData?.rental?.final_amount)"></span></div>
            </div>

            <div class="space-y-3">
                <a :href="'/company/rentals/' + (successData?.rental?.id)" class="block w-full py-3 bg-[#00BCD4] hover:bg-[#00a0b5] text-slate-900 font-black text-xs text-center uppercase tracking-widest rounded-xl transition-all">📄 View Rental Record</a>
                <button @click="window.location.reload()" class="block w-full py-3 bg-slate-800 hover:bg-slate-700 text-white font-black text-xs text-center uppercase tracking-widest rounded-xl transition-all border border-slate-700">+ Create Another Booking</button>
            </div>
        </div>
    </div>

</div>

<script>
    function rentalBooking() {
        return {
            step: 1,
            customerId: '{{ old('customer_id') }}',
            rentalType: '{{ old('rental_type', 'daily') }}',
            rateType: '{{ old('rate_type', 'daily') }}',
            pickupTime: '{{ old('start_date') }}',
            dropoffTime: '{{ old('end_date') }}',
            vehicleId: '{{ old('vehicle_id') }}',
            driverId: '{{ old('driver_id') }}',
            pickupLocation: '{{ old('pickup_location') }}',
            dropoffLocation: '{{ old('dropoff_location') }}',
            options: { overtime: false, gps: false, childSeat: false, insurance: false },
            notes: '{{ old('notes') }}',
            generateAgreement: true,
            agreementTemplate: 'standard',
            baseRateAmount: parseFloat('{{ old('rate_amount', 0) }}') || 0,
            securityDeposit: parseFloat('{{ old('security_deposit', 0) }}') || 0,
            discount: parseFloat('{{ old('discount', 0) }}') || 0,
            optimizing: false,
            submitting: false,
            successData: null,
            
            customers: @json($customers),
            vehicles: @json($vehicles),

            get selectedCustomer() {
                return this.customerId ? this.customers.find(c => c.id == this.customerId) : null;
            },
            get selectedVehicle() {
                return this.vehicleId ? this.vehicles.find(v => v.id == this.vehicleId) : null;
            },
            get durationDays() {
                if (!this.pickupTime || !this.dropoffTime) return 0;
                const start = new Date(this.pickupTime);
                const end = new Date(this.dropoffTime);
                const diffMs = end - start;
                return diffMs > 0 ? Math.ceil(diffMs / (1000 * 60 * 60 * 24)) : 0;
            },
            get durationLabel() {
                const days = this.durationDays;
                if(days === 0) return '';
                return `${days} Day${days > 1 ? 's' : ''}`;
            },
            get extrasTotal() {
                let total = 0;
                const days = Math.max(1, this.durationDays);
                if (this.options.childSeat) total += 25 * days;
                // Add more logic here if needed
                return total;
            },
            get subtotal() {
                return Math.max(0, this.baseRateAmount + this.extrasTotal - this.discount);
            },
            get vat() {
                return this.subtotal * 0.05;
            },
            get total() {
                return this.subtotal + this.vat;
            },
            
            updateRate(vId) {
                const v = this.vehicles.find(v => v.id == vId);
                if(v && v.daily_rate) {
                    this.baseRateAmount = parseFloat(v.daily_rate) * Math.max(1, this.durationDays);
                }
            },

            async optimizePricing() {
                if (!this.pickupTime || !this.customerId || !this.vehicleId) {
                    alert('Select Customer, Schedule & Vehicle first.');
                    return;
                }
                this.optimizing = true;
                try {
                    const response = await fetch('{{ route('company.intelligence.calculate-rate') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ customer_id: this.customerId, vehicle_id: this.vehicleId, start_date: this.pickupTime })
                    });
                    if (response.ok) {
                        const data = await response.json();
                        this.baseRateAmount = data.optimized_rate;
                    }
                } catch(e) { console.error(e); }
                setTimeout(() => { this.optimizing = false; }, 500);
            },

            fmt(value) {
                return new Intl.NumberFormat('en-AE', { style: 'currency', currency: 'AED', minimumFractionDigits: 2 }).format(value || 0);
            },

            async submit() {
                if(this.step < 4) {
                    this.step = 4;
                    return;
                }
                
                const form = document.getElementById('rentalForm');
                if(!form.reportValidity()) return;

                this.submitting = true;
                const formData = new FormData(form);
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    
                    const data = await response.json();
                    
                    if(response.ok && data.success) {
                        this.successData = data;
                    } else {
                        // Handle Laravel validation errors (422)
                        if(data.errors) {
                            alert('Validation Error:\n' + Object.values(data.errors).flat().join('\n'));
                            this.step = 1;
                        } else if(data.message) {
                            alert('Server Error: ' + data.message);
                        } else if(data.error) {
                            alert('Error: ' + data.error);
                        } else {
                            // Fallback if not standard JSON
                            alert('Error saving rental. Check your inputs.\nResponse: ' + JSON.stringify(data));
                        }
                    }
                } catch (e) {
                    console.error(e);
                    alert('Network error or invalid server response.');
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
@endsection