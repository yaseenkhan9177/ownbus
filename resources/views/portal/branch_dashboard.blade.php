@extends('layouts.company')

@section('title', 'Branch Operations Console')

@section('content')
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">

    <!-- 1️⃣ Summary Cards (Top Section) -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        {{-- Today's Rentals --}}
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Today's Rentals</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-white">{{ $data['summary']['today_rentals'] }}</h3>
                <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Rentals --}}
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Active Now</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-blue-400">{{ $data['summary']['active_rentals'] }}</h3>
                <div class="p-2 rounded-lg bg-blue-500/10 text-blue-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Available Vehicles --}}
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Available Fleet</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-emerald-400">{{ $data['summary']['available_vehicles'] }}</h3>
                <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Maintenance --}}
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">In Maintenance</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-amber-500">{{ $data['summary']['maintenance_vehicles'] }}</h3>
                <div class="p-2 rounded-lg bg-amber-500/10 text-amber-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Today's Revenue --}}
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Today's Revenue</p>
            <div class="flex items-end justify-between">
                <h3 class="text-xl font-black text-white">AED {{ number_format($data['summary']['today_revenue'], 0) }}</h3>
                <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Payments --}}
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Pending AR</p>
            <div class="flex items-end justify-between">
                <h3 class="text-xl font-black text-rose-500">AED {{ number_format($data['summary']['pending_payments'], 0) }}</h3>
                <div class="p-2 rounded-lg bg-rose-500/10 text-rose-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- 2️⃣ Active Rentals Table (Main Section) -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white uppercase tracking-tight">Active Operations</h2>
                    <a href="{{ route('company.rentals.index') }}" class="text-xs font-bold text-blue-400 hover:underline">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-800 bg-slate-800/20">
                                <th class="px-6 py-4">Contract No</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Vehicle</th>
                                <th class="px-6 py-4">Driver</th>
                                <th class="px-6 py-4">End Date</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($data['active_rentals'] as $rental)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-blue-400">{{ $rental->contract_no ?? $rental->rental_number }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $rental->customer->name ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $rental->customer->phone ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $rental->vehicle->vehicle_number ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $rental->vehicle->name ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-300">{{ $rental->driver->name ?? 'Self Drive' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-300">{{ $rental->end_date->format('d M, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                        {{ $rental->status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500 animate-pulse' }}">
                                        {{ $rental->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-sm">No active rentals discovered for this branch yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3️⃣ & 4️⃣ Vehicle & Driver Status Overview (Bottom Section) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Vehicle Status --}}
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-3xl">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Fleet Allocation</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_theme('colors.emerald.500')]"></div>
                                <span class="text-sm text-slate-300">Available Vehicles</span>
                            </div>
                            <span class="text-sm font-bold text-white">{{ $data['vehicle_status']['available'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-rose-500 shadow-[0_0_8px_theme('colors.rose.500')]"></div>
                                <span class="text-sm text-slate-300">Rented (Active)</span>
                            </div>
                            <span class="text-sm font-bold text-white">{{ $data['vehicle_status']['rented'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_8px_theme('colors.amber.500')]"></div>
                                <span class="text-sm text-slate-300">Under Maintenance</span>
                            </div>
                            <span class="text-sm font-bold text-white">{{ $data['vehicle_status']['maintenance'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Driver Status --}}
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-3xl">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Driver Readiness</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-300">Available Drivers</span>
                            <span class="text-sm font-bold text-emerald-400">{{ $data['driver_status']['available'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-300">Currently On Trip</span>
                            <span class="text-sm font-bold text-blue-400">{{ $data['driver_status']['on_trip'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-300">License Expiry Risks</span>
                            <span class="text-sm font-bold text-rose-500">{{ $data['driver_status']['license_expiring'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5️⃣ Alerts Section (Right Sidebar) -->
        <div class="space-y-6">
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-3xl h-full shadow-2xl">
                <div class="flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Priority Alerts</h3>
                </div>

                <div class="space-y-4">
                    @forelse($data['alerts'] as $alert)
                    <div class="p-4 rounded-2xl border 
                        @if($alert['type'] === 'danger') border-rose-500/30 bg-rose-500/5 @elseif($alert['type'] === 'warning') border-amber-500/30 bg-amber-500/5 @else border-blue-500/30 bg-blue-500/5 @endif group transition-all hover:scale-[1.02]">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">{{ $alert['message'] }}</p>
                                <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-tighter">{{ $alert['meta'] }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10">
                        <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4 text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Operations Steady</p>
                        <p class="text-[9px] text-slate-600 mt-1 uppercase">No Priority Alerts</p>
                    </div>
                    @endforelse
                </div>

                <div class="mt-8 pt-6 border-t border-slate-800">
                    <button class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                        Operational Logs
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection