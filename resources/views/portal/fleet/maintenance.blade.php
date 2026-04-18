@extends('layouts.company')

@section('content')
<div class="space-y-6">
    <!-- Maintenance Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1 uppercase tracking-tighter">FLEET_HEALTH_HUB</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Maintenance Monitor & Predictive Intelligence</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
                <i class="bi bi-clock-history"></i>
                DOWNTIME_PROJECTION
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-blue-500/20">
                + SCHEDULE_PROTOCOL
            </button>
        </div>
    </div>

    <!-- Health Stats Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Active_Ops_Downtime</h4>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-rose-500">{{ $logs->where('status', 'active')->count() }}</span>
                <span class="text-[10px] font-bold text-slate-400">UNITS_IN_DOCK</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pending_Odo_Expiries</h4>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-amber-500">{{ $upcoming->count() }}</span>
                <span class="text-[10px] font-bold text-slate-400">CRITICAL_RANGE</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total_Ops_Completion</h4>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $logs->where('status', 'completed')->count() }}</span>
                <span class="text-[10px] font-bold text-slate-400">HISTORICAL_LOGS</span>
            </div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl shadow-slate-900/10 flex items-center justify-between overflow-hidden relative">
            <div class="z-10">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Fleet_Readiness</h4>
                <div class="text-2xl font-black text-white">94.2%</div>
            </div>
            <i class="bi bi-shield-check text-4xl text-white/10 absolute -right-2 top-2 z-0"></i>
        </div>
    </div>

    <!-- Predictive Intel Stack -->
    @if($upcoming->isNotEmpty())
    <div class="bg-white dark:bg-slate-900 rounded-3xl border-2 border-amber-500/20 dark:border-amber-500/10 p-6 shadow-lg shadow-amber-500/5">
        <h3 class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-6 flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            PREDICTIVE_MAINTENANCE_ALERTS
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($upcoming as $vehicle)
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 p-4 transition-all hover:scale-[1.02] cursor-pointer group" onclick="window.location='{{ route("company.fleet.show", $vehicle) }}';">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $vehicle->name }}</div>
                        <div class="text-sm font-black text-slate-900 dark:text-white group-hover:text-blue-500 transition-colors uppercase tracking-tight">{{ $vehicle->vehicle_number }}</div>
                    </div>
                    <div class="px-2 py-0.5 rounded-lg text-[8px] font-black bg-amber-500 text-white uppercase tracking-widest">CRITICAL</div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-700">
                    <div class="space-y-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase block tracking-tighter">Remaining_Range</span>
                        <span class="text-xs font-black text-orange-600 font-mono">{{ number_format($vehicle->next_service_odometer - $vehicle->current_odometer) }} KM</span>
                    </div>
                    <div class="text-right space-y-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase block tracking-tighter">Target_Odo</span>
                        <span class="text-xs font-black text-slate-900 dark:text-white font-mono">{{ number_format($vehicle->next_service_odometer) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Maintenance Ledger Grid -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-slate-50 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ops_Downtime_Ledger</h3>
            <div class="flex items-center gap-4">
                <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest">HISTORICAL_STREAM</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/30 dark:bg-slate-800/20 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="px-8 py-4">Asset_Profile</th>
                        <th class="px-8 py-4">Temporal_Window</th>
                        <th class="px-8 py-4">Reason_Protocol</th>
                        <th class="px-8 py-4">Downtime_Intel</th>
                        <th class="px-8 py-4 text-right">Operational_State</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-8 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-blue-500 transition-colors">
                                    <i class="bi bi-bus-front text-lg"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-blue-500 transition-colors">{{ $log->vehicle->vehicle_number }}</div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase">{{ $log->vehicle->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="text-[10px] font-black text-slate-900 dark:text-white tracking-tighter">{{ $log->start_datetime->format('d M') }} - {{ $log->end_datetime->format('d M, Y') }}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">DOWNTIME_DURATION: {{ $log->start_datetime->diffInDays($log->end_datetime) }} DAYS</div>
                        </td>
                        <td class="px-8 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[9px] font-black bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 ring-1 ring-slate-200 dark:ring-slate-700 uppercase tracking-widest">
                                {{ $log->reason_type }}
                            </span>
                        </td>
                        <td class="px-8 py-4">
                            <p class="text-[10px] font-bold text-slate-600 dark:text-slate-400 max-w-[200px] truncate group-hover:whitespace-normal group-hover:truncate-none transition-all">{{ $log->description ?? 'NO_DESCRIPTION_PROVIDED' }}</p>
                        </td>
                        <td class="px-8 py-4 text-right">
                            @php
                            $now = now();
                            $isActive = $now->between($log->start_datetime, $log->end_datetime);
                            $isCompleted = $now->gt($log->end_datetime);
                            @endphp
                            @if($isActive)
                            <div class="flex flex-col items-end gap-1">
                                <span class="px-2 py-0.5 rounded-full text-[8px] font-black bg-rose-500 text-white uppercase tracking-widest animate-pulse">ACTIVE_DOWNTIME</span>
                                <span class="text-[8px] font-black text-rose-500/50 uppercase tracking-tighter">UNIT_CURRENTLY_LOCKED</span>
                            </div>
                            @elseif($isCompleted)
                            <span class="px-2 py-0.5 rounded-full text-[8px] font-black bg-emerald-500/10 text-emerald-500 ring-1 ring-emerald-500/20 uppercase tracking-widest">SETTLED_ALPHA</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[8px] font-black bg-blue-500/10 text-blue-500 ring-1 ring-blue-500/20 uppercase tracking-widest">PROJECTED_OPS</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                            ZERO_MAINTENANCE_LOGS_DETECTED_IN_FLEET_CHRONICLE
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-8 py-4 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection