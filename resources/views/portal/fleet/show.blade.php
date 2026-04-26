@extends('layouts.company')

@section('title', 'Asset Intelligence - ' . $vehicle->vehicle_number)

@section('header_title')
<div class="flex items-center space-x-4">
    <a href="{{ route('company.fleet.index') }}" class="p-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl text-slate-400 hover:text-cyan-500 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
    </a>
    <div class="flex flex-col">
        <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none">{{ $vehicle->vehicle_number }}</h1>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">{{ $vehicle->name }} &bull; {{ $vehicle->model }}</span>
    </div>
</div>
@endsection

@section('content')
<div x-data="{ activeTab: 'telemetry' }" class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">

    {{-- 1. Asset Header Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1 space-y-6">
            {{-- Photo Card --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm aspect-square relative group">
                @if($vehicle->image_path)
                <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->name }}" class="w-full h-full object-cover">
                @else
                <div class="w-full h-full flex flex-col items-center justify-center text-slate-200 dark:text-slate-800">
                    <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent flex flex-col justify-end p-6">
                    <div class="flex items-center justify-between">
                        @php
                        $statusConfig = [
                        'available' => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'label' => 'AVAILABLE'],
                        'rented' => ['bg' => 'bg-cyan-500', 'text' => 'text-white', 'label' => 'ON TRIP / RENTED'],
                        'maintenance' => ['bg' => 'bg-amber-500', 'text' => 'text-white', 'label' => 'IN SERVICE'],
                        'inactive' => ['bg' => 'bg-slate-500', 'text' => 'text-white', 'label' => 'OFFLINE'],
                        ];
                        $cfg = $statusConfig[$vehicle->status] ?? $statusConfig['inactive'];
                        @endphp
                        <span class="px-2 py-0.5 rounded {{ $cfg['bg'] }} {{ $cfg['text'] }} text-[10px] font-black uppercase tracking-widest">{{ $cfg['label'] }}</span>
                        <a href="{{ route('company.fleet.edit', $vehicle) }}" class="p-2 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-all backdrop-blur-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Financial Performance --}}
            <div class="bg-cyan-600 rounded-2xl p-6 text-white shadow-lg shadow-cyan-600/20 relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-cyan-200 uppercase tracking-widest mb-1">Lifetime Yield</p>
                    <p class="text-3xl font-black">AED {{ number_format($vehicle->total_revenue ?? 0, 0) }}</p>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-bold text-cyan-200 uppercase">Avg Rental</p>
                            <p class="text-sm font-black">AED {{ number_format($vehicle->daily_rate, 0) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-cyan-200 uppercase">Util. Rate</p>
                            <p class="text-sm font-black">84.2%</p>
                        </div>
                    </div>
                </div>
                {{-- Decorative background --}}
                <div class="absolute -right-4 -bottom-4 text-cyan-500 opacity-20">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- 2. Telemetry and Tabs --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Quick Telemetry Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-4 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Odometer</p>
                    <p class="text-lg font-black text-slate-900 dark:text-white font-mono">{{ number_format($vehicle->current_odometer) }} <span class="text-[10px] text-slate-400">KM</span></p>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-4 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Next Service</p>
                    <p class="text-lg font-black text-amber-500 font-mono">{{ number_format($vehicle->next_service_odometer) }} <span class="text-[10px] text-slate-400">KM</span></p>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-4 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Fuel Type</p>
                    <p class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $vehicle->fuel_type }}</p>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-4 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Asset Class</p>
                    <p class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $vehicle->type }}</p>
                </div>
            </div>

            {{-- Main Tabbed Interface --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-50 dark:border-slate-800 px-6">
                    <nav class="-mb-px flex space-x-6">
                        <button @click="activeTab = 'telemetry'" :class="{ 'border-cyan-500 text-cyan-500': activeTab === 'telemetry', 'border-transparent text-slate-400 hover:text-slate-200': activeTab !== 'telemetry' }" class="py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all">
                            Intelligence
                        </button>
                        <button @click="activeTab = 'maintenance'" :class="{ 'border-cyan-500 text-cyan-500': activeTab === 'maintenance', 'border-transparent text-slate-400 hover:text-slate-200': activeTab !== 'maintenance' }" class="py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all">
                            Svc Log
                        </button>
                        <button @click="activeTab = 'rentals'" :class="{ 'border-cyan-500 text-cyan-500': activeTab === 'rentals', 'border-transparent text-slate-400 hover:text-slate-200': activeTab !== 'rentals' }" class="py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all">
                            Deployment History
                        </button>
                        <button @click="activeTab = 'timeline'" :class="{ 'border-cyan-500 text-cyan-500': activeTab === 'timeline', 'border-transparent text-slate-400 hover:text-slate-200': activeTab !== 'timeline' }" class="py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all">
                            Activity Timeline
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    {{-- Intelligence / Telemetry Tab --}}
                    <div x-show="activeTab === 'telemetry'" class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-in fade-in slide-in-from-left-2 duration-300">
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2">Compliance & Registration</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">State Registration</span>
                                        <span class="text-xs font-bold">{{ $vehicle->registration_emirate ?? 'N/A' }} / {{ $vehicle->plate_category ?? 'N/A' }}</span>
                                    </div>
                                    @if($vehicle->plate_number)
                                    <span class="text-[10px] font-mono font-black px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white rounded">{{ $vehicle->plate_number }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Mulkiya Expiry</span>
                                        <span class="text-xs font-bold">{{ $vehicle->registration_expiry ? $vehicle->registration_expiry->format('d M Y') : 'UNREGISTERED' }}</span>
                                    </div>
                                    @if($vehicle->registration_expiry && $vehicle->registration_expiry->isPast())
                                    <span class="text-[10px] font-black px-2 py-0.5 bg-rose-500/10 text-rose-500 rounded uppercase animate-pulse">EXPIRED</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Insurance Expiry</span>
                                        <span class="text-xs font-bold">{{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d M Y') : 'UNINSURED' }}</span>
                                    </div>
                                    @if($vehicle->insurance_expiry && $vehicle->insurance_expiry->isPast())
                                    <span class="text-[10px] font-black px-2 py-0.5 bg-rose-500/10 text-rose-500 rounded uppercase animate-pulse">EXPIRED</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2">Current Deployment</h3>
                            <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
                                @if($vehicle->currentRental)
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Assigned To</p>
                                        <p class="text-sm font-black text-slate-900 dark:text-white uppercase">{{ $vehicle->currentRental->customer->name }}</p>
                                        <a href="{{ route('company.rentals.show', $vehicle->currentRental) }}" class="text-[9px] font-bold text-cyan-500 uppercase tracking-widest hover:underline">Mission #{{ $vehicle->currentRental->rental_number }}</a>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-800 grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">From</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $vehicle->currentRental->start_date->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Return By</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $vehicle->currentRental->end_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                @else
                                <div class="flex flex-col items-center justify-center py-4 text-center">
                                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <p class="text-xs font-black text-emerald-500 uppercase tracking-widest">Available for Duty</p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">No active missions recorded</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-amber-500 pl-2">Plate & Fine Info</h3>
                            @if($vehicle->plate_number_dp)
                            @php
                                $flags = [
                                    'Dubai'=>'🇦🇪','Abu Dhabi'=>'🇦🇪','Sharjah'=>'🇦🇪','Ajman'=>'🇦🇪','RAK'=>'🇦🇪','Fujairah'=>'🇦🇪','UAQ'=>'🇦🇪',
                                    'Saudi Arabia'=>'🇸🇦','Kuwait'=>'🇰🇼','Bahrain'=>'🇧🇭','Qatar'=>'🇶🇦','Oman'=>'🇴🇲'
                                ];
                                $flag = $flags[$vehicle->plate_source] ?? '🏳️';
                                $links = [
                                    'Dubai' => 'https://www.dubaipolice.gov.ae/app/services/fine-payment/search',
                                    'Abu Dhabi' => 'https://www.adpolice.gov.ae/en/services/traffic-services/traffic-fines-inquiry',
                                    'Sharjah' => 'https://www.sharjahpolice.gov.ae',
                                    'Ajman' => 'https://www.ajmanpolice.gov.ae',
                                    'RAK' => 'https://www.rakpolice.gov.ae',
                                    'Fujairah' => 'https://www.fujairahpolice.gov.ae',
                                    'UAQ' => 'https://uaqpolice.gov.ae',
                                    'Saudi Arabia' => 'https://www.absher.sa',
                                    'Kuwait' => 'https://www.moi.gov.kw',
                                    'Bahrain' => 'https://www.bahrain.bh',
                                    'Qatar' => 'https://www.moi.gov.qa',
                                    'Oman' => 'https://www.rop.gov.om'
                                ];
                                $officialLink = $links[$vehicle->plate_source] ?? '#';
                            @endphp
                            <div class="p-4 bg-slate-900 rounded-xl border border-slate-700/50 flex flex-col space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 border border-amber-500/50 flex items-center justify-center text-xl">{{ $flag }}</div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $vehicle->plate_source }} Plate</p>
                                        <p class="text-sm font-mono font-black text-white">{{ $vehicle->plate_code_dp ? $vehicle->plate_code_dp . ' ' : '' }}{{ $vehicle->plate_number_dp }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ $officialLink }}" target="_blank" rel="noopener" class="flex-1 flex justify-center items-center px-3 py-2 bg-amber-500 hover:bg-amber-400 text-slate-900 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all shadow-lg shadow-amber-500/20">
                                        Check Fines
                                    </a>
                                    <a href="{{ route('company.fines.checker') }}" class="flex items-center justify-center px-3 py-2 bg-slate-800 hover:bg-slate-700 text-white text-[10px] font-black uppercase tracking-widest rounded-lg transition-all border border-slate-700">
                                        Internal DB
                                    </a>
                                </div>
                            </div>
                            @else
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center justify-center text-center">
                                <p class="text-sm font-bold text-slate-500 uppercase">No Plate Data</p>
                                <a href="{{ route('company.fleet.edit', $vehicle) }}" class="mt-2 text-[10px] font-black text-cyan-500 uppercase tracking-widest hover:underline">Configure Asset</a>
                            </div>
                            @endif
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2">Specifications</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Seats</span>
                                    <span class="text-sm font-black text-slate-900 dark:text-white uppercase">{{ $vehicle->seating_capacity }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Color</span>
                                    <span class="text-sm font-black text-slate-900 dark:text-white uppercase">{{ $vehicle->color ?? 'SPECIFY' }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">VIN</span>
                                    <span class="text-[10px] font-mono font-bold text-slate-900 dark:text-white uppercase">{{ $vehicle->vehicle_number }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Company</span>
                                    <span class="text-[10px] font-black text-cyan-500 uppercase">{{ auth()->user()->company->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Maintenance Log Tab --}}
                    <div x-show="activeTab === 'maintenance'" style="display: none;" class="animate-in fade-in slide-in-from-left-2 duration-300 space-y-4">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2">Service History</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-gray-50 dark:border-slate-800">
                                        <th class="py-2 px-2">Cycle Date</th>
                                        <th class="py-2 px-2">Operation Type</th>
                                        <th class="py-2 px-2">Summary</th>
                                        <th class="py-2 px-2 text-right">Officer</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                                    @forelse($maintenanceLogs as $log)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                        <td class="py-3 px-2">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($log->start_datetime)->format('d M Y') }}</span>
                                                <span class="text-[9px] text-slate-400 uppercase font-bold">{{ \Carbon\Carbon::parse($log->start_datetime)->format('H:i') }} &mdash; {{ \Carbon\Carbon::parse($log->end_datetime)->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-2">
                                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-500 uppercase tracking-tighter border border-amber-500/20">{{ $log->reason_type }}</span>
                                        </td>
                                        <td class="py-3 px-2">
                                            <p class="text-xs text-slate-600 dark:text-slate-400 max-w-xs truncate">{{ $log->description ?? 'ROUTINE SERVICE' }}</p>
                                        </td>
                                        <td class="py-3 px-2 text-right">
                                            <p class="text-[10px] font-bold text-slate-900 dark:text-white uppercase leading-none">{{ $log->creator->name ?? 'System' }}</p>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-xs text-slate-400 font-bold uppercase tracking-widest">No maintenance datasets found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Deployment History Tab --}}
                    <div x-show="activeTab === 'rentals'" style="display: none;" class="animate-in fade-in slide-in-from-left-2 duration-300">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2 mb-4">Rental Logistics</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-gray-50 dark:border-slate-800">
                                        <th class="py-2 px-2">Job ID</th>
                                        <th class="py-2 px-2">Timeline</th>
                                        <th class="py-2 px-2">Customer Entity</th>
                                        <th class="py-2 px-2">Status</th>
                                        <th class="py-2 px-2 text-right">Yield</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                                    @forelse($vehicle->rentals as $rental)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                        <td class="py-3 px-2 font-mono text-[10px] text-slate-400">#{{ substr($rental->uuid, 0, 8) }}</td>
                                        <td class="py-3 px-2">
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $rental->start_date->format('d M') }} &mdash; {{ $rental->end_date->format('d M Y') }}</span>
                                        </td>
                                        <td class="py-3 px-2">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $rental->customer->name ?? 'GUEST' }}</span>
                                                <span class="text-[9px] text-slate-500 font-bold">{{ $rental->customer->phone ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-2">
                                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase tracking-tighter">{{ $rental->status }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-right font-black text-xs text-slate-900 dark:text-white">
                                            AED {{ number_format($rental->final_amount, 0) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-xs text-slate-400 font-bold uppercase tracking-widest">No deployment records found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Activity Timeline Tab --}}
                    <div x-show="activeTab === 'timeline'" style="display: none;" class="animate-in fade-in slide-in-from-left-2 duration-300">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-l-2 border-cyan-500 pl-2 mb-4">Unified Activity Timeline</h3>
                        <div class="relative border-l border-gray-200 dark:border-slate-800 ml-3">
                            @forelse($timeline as $event)
                            <div class="mb-6 ml-6 relative">
                                <span class="absolute -left-[35px] flex items-center justify-center w-8 h-8 rounded-full ring-4 ring-white dark:ring-slate-900 
                                    @if($event['type'] === 'trip') bg-cyan-100 text-cyan-600 
                                    @elseif($event['type'] === 'maintenance') bg-amber-100 text-amber-600 
                                    @elseif($event['type'] === 'fine') bg-rose-100 text-rose-600 
                                    @elseif($event['type'] === 'invoice') bg-emerald-100 text-emerald-600 
                                    @else bg-gray-100 text-gray-500 @endif">
                                    @if($event['type'] === 'trip')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                    @elseif($event['type'] === 'maintenance')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37..."></path></svg>
                                    @elseif($event['type'] === 'fine')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @elseif($event['type'] === 'invoice')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @endif
                                </span>
                                <div class="bg-gray-50 dark:bg-slate-800/30 border border-gray-100 dark:border-slate-800 rounded-lg p-3">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $event['label'] }}</h4>
                                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mt-1">{{ \Carbon\Carbon::parse($event['date'])->diffForHumans() }} &mdash; {{ \Carbon\Carbon::parse($event['date'])->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="ml-6 py-4">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">No activity recorded for this vehicle.</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection