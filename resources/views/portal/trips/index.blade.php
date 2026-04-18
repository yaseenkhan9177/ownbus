@extends('layouts.company')

@section('title', 'Trip Management')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1 uppercase">OPERATIONAL_LOG</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Fleet Kinetic Tracking & Performance</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="p-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- KPI Strip --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden relative group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="bi bi-truck text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">TOTAL_TRIPS</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_trips']) }}</div>
                <div class="mt-2 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Lifetime Operations</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden relative group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="bi bi-activity text-6xl text-emerald-500"></i>
            </div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ACTIVE_NOW</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['active_trips'] }}</div>
                <div class="mt-2 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-tighter">Live Fleet Kinetic</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden relative group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="bi bi-check-circle text-6xl text-blue-500"></i>
            </div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">COMPLETED_TODAY</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['completed_today'] }}</div>
                <div class="mt-2 flex items-center gap-1">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Success Rate: 100%</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden relative group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="bi bi-speedometer2 text-6xl text-amber-500"></i>
            </div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">KINETIC_DISTANCE</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_km_today']) }} <span class="text-xs font-bold text-slate-400 uppercase">KM</span></div>
                <div class="mt-2 flex items-center gap-1 text-[9px] font-bold text-amber-500 uppercase tracking-tighter">
                    <i class="bi bi-graph-up"></i> Total Movement Today
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">STATUS</label>
                <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 appearance-none">
                    <option value="">ALL_PROTOCOLS</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>PENDING</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>IN_PROGRESS</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>COMPLETED</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>CANCELLED</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">VEHICLE_UNIT</label>
                <select name="vehicle_id" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 appearance-none">
                    <option value="">ALL_UNITS</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->vehicle_number }} — {{ $v->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">OPERATOR</label>
                <select name="driver_id" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 appearance-none">
                    <option value="">ALL_OPERATORS</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->user?->name ?? 'Operator #'.$d->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-32">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">TIMELINE_START</label>
                <input type="date" name="from_date" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500" value="{{ request('from_date') }}">
            </div>

            <div class="w-32">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">TIMELINE_END</label>
                <input type="date" name="to_date" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500" value="{{ request('to_date') }}">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl transition-all shadow-lg shadow-blue-500/20">
                    <i class="bi bi-funnel-fill text-xs"></i>
                </button>
                <a href="{{ route('company.trips.index') }}" class="bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 p-3 rounded-xl hover:scale-105 transition-all">
                    <i class="bi bi-arrow-counterclockwise text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Trips Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">TRIP_IDENTIFIER</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">KINETIC_UNIT</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">OPERATOR</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">PROTOCOL_STATE</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">TEMPORAL_MARK</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">DISTANCE</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">OPERATIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($trips as $trip)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 text-[10px] font-black">
                                    #{{ $trip->id }}
                                </div>
                                @if($trip->rental)
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                                        REF: <span class="text-slate-600 dark:text-slate-300">{{ $trip->rental->rental_number ?? '—' }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($trip->vehicle)
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ $trip->vehicle->vehicle_number }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase truncate max-w-[120px]">{{ $trip->vehicle->name }}</span>
                                </div>
                            @else
                                <span class="text-[10px] font-black text-slate-300">VOID_UNIT</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase truncate max-w-[150px]">
                                {{ $trip->driver?->user?->name ?? 'VOID_OPERATOR' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                @php
                                    $states = [
                                        'pending'     => 'bg-amber-500/10 text-amber-500 ring-amber-500/20',
                                        'in_progress' => 'bg-blue-500/10 text-blue-500 ring-blue-500/20',
                                        'completed'   => 'bg-emerald-500/10 text-emerald-500 ring-emerald-500/20',
                                        'cancelled'   => 'bg-rose-500/10 text-rose-500 ring-rose-500/20',
                                    ];
                                    $state = $states[$trip->status] ?? 'bg-slate-500/10 text-slate-500 ring-slate-500/20';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest ring-1 {{ $state }}">
                                    {{ str_replace('_', ' ', $trip->status) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-900 dark:text-white">
                                    {{ $trip->actual_start?->format('d M / H:i') ?? 'UNSPECIFIED' }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase">
                                    {{ $trip->getFormattedDuration() }} ELAPSED
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($trip->distance_km)
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($trip->distance_km) }}</span>
                                    <span class="text-[9px] font-black text-slate-400 uppercase">KM</span>
                                </div>
                            @else
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('company.trips.show', $trip) }}" class="p-2 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-all">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if(!$trip->isCompleted())
                                <form action="{{ route('company.trips.cancel', $trip) }}" method="POST" onsubmit="return confirm('INITIATE_CANCELLATION_PROTOCOL?')">
                                    @csrf @method('PATCH')
                                    <button class="p-2 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition-all">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-200 dark:text-slate-700">
                                    <i class="bi bi-inbox text-3xl"></i>
                                </div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest text">ZERO_TRIP_DATA_LOGGED</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trips->hasPages())
        <div class="px-6 py-4 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $trips->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
