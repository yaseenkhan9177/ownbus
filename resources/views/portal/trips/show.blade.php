@extends('layouts.company')

@section('title', 'Trip #' . $trip->id)

@section('content')
<div class="space-y-6">
    {{-- Breadcrumb & Identity --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-[10px] font-black text-blue-500 uppercase tracking-widest">
                <a href="{{ route('company.trips.index') }}" class="hover:text-blue-600 transition-colors">OPERATIONAL_LOG</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-400">TRIP_DOSSIER_#{{ $trip->id }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">MISSION_INTEL</h1>
        </div>
        
        <div class="flex items-center gap-3">
            @php
                $states = [
                    'pending'     => 'bg-amber-500/10 text-amber-500 ring-amber-500/20',
                    'in_progress' => 'bg-blue-500/10 text-blue-500 ring-blue-500/20',
                    'completed'   => 'bg-emerald-500/10 text-emerald-500 ring-emerald-500/20',
                    'cancelled'   => 'bg-rose-500/10 text-rose-500 ring-rose-500/20',
                ];
                $state = $states[$trip->status] ?? 'bg-slate-500/10 text-slate-500 ring-slate-500/20';
            @endphp
            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest ring-1 shadow-sm {{ $state }}">
                PROTOCOL: {{ str_replace('_', ' ', $trip->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Dossier Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Operational Timeline Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-clock-history text-blue-500"></i> TEMPORAL_MARKINGS
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">SCHEDULED_WINDOW</div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">
                                    {{ $trip->scheduled_start?->format('d M Y, H:i') ?? 'UNSCHEDULED' }}
                                    @if($trip->scheduled_end)
                                        <div class="flex items-center gap-2 mt-1 text-slate-400">
                                            <i class="bi bi-arrow-right"></i>
                                            <span>{{ $trip->scheduled_end->format('d M Y, H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ACTUAL_EXECUTION</div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">
                                    @if($trip->actual_start)
                                        {{ $trip->actual_start->format('d M Y, H:i') }}
                                        @if($trip->actual_end)
                                            <div class="flex items-center gap-2 mt-1 text-emerald-500">
                                                <i class="bi bi-check-all"></i>
                                                <span>{{ $trip->actual_end->format('d M Y, H:i') }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-slate-400 uppercase tracking-widest italic">Awaiting Protocol Start</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Performance Metrics --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                        <div class="text-center p-4 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">DURATION</div>
                            <div class="text-sm font-black text-blue-600 dark:text-blue-400">{{ $trip->getFormattedDuration() }}</div>
                        </div>
                        <div class="text-center p-4 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">DISTANCE</div>
                            <div class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ $trip->distance_km ? number_format($trip->distance_km).' KM' : '—' }}</div>
                        </div>
                        <div class="text-center p-4 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">FUEL_LOG</div>
                            <div class="text-sm font-black text-amber-600 dark:text-amber-400">{{ $trip->fuel_used_liters ? $trip->fuel_used_liters.' L' : '—' }}</div>
                        </div>
                        <div class="text-center p-4 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">EFFICIENCY</div>
                            <div class="text-sm font-black text-indigo-600 dark:text-indigo-400">{{ $trip->getFuelEfficiency() ? $trip->getFuelEfficiency().' KM/L' : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Geospatial & Telemetry Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-geo-fill text-emerald-500"></i> GEOSPATIAL_LOGS
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Pickup --}}
                        <div class="relative pl-6 border-l-2 border-emerald-500/30">
                            <div class="absolute -left-[5px] top-0 w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></div>
                            <div class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">ORIGIN_POINT</div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white leading-relaxed">{{ $trip->pickup_location ?? 'UNSPECIFIED_POSITION' }}</div>
                            @if($trip->start_lat)
                                <div class="mt-2 inline-flex items-center gap-2 px-2 py-1 bg-slate-50 dark:bg-slate-800 rounded-lg text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                    <i class="bi bi-crosshair text-rose-500"></i> GPS: {{ $trip->start_lat }}, {{ $trip->start_lng }}
                                </div>
                            @endif
                        </div>

                        {{-- Dropoff --}}
                        <div class="relative pl-6 border-l-2 border-rose-500/30">
                            <div class="absolute -left-[5px] top-0 w-2 h-2 rounded-full bg-rose-500 ring-4 ring-rose-500/20"></div>
                            <div class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">DESTINATION_LOCK</div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white leading-relaxed">{{ $trip->dropoff_location ?? 'UNSPECIFIED_POSITION' }}</div>
                            @if($trip->end_lat)
                                <div class="mt-2 inline-flex items-center gap-2 px-2 py-1 bg-slate-50 dark:bg-slate-800 rounded-lg text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                    <i class="bi bi-crosshair text-rose-500"></i> GPS: {{ $trip->end_lat }}, {{ $trip->end_lng }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Telemetry --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 pt-6 border-t border-slate-50 dark:border-slate-800">
                        <div>
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">ODOMETER_INIT</div>
                            <div class="text-sm font-black text-slate-900 dark:text-white">{{ $trip->odometer_start ? number_format($trip->odometer_start).' KM' : 'ZERO_MARK' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">ODOMETER_TERM</div>
                            <div class="text-sm font-black text-slate-900 dark:text-white">{{ $trip->odometer_end ? number_format($trip->odometer_end).' KM' : 'PENDING' }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">OPERATOR_RATING</div>
                            <div class="flex items-center gap-1">
                                @if($trip->driver_rating)
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $trip->driver_rating ? '-fill text-amber-400 shadow-sm' : ' text-slate-200 dark:text-slate-800' }}"></i>
                                    @endfor
                                @else
                                    <span class="text-[10px] font-black text-slate-300 uppercase italic">Awaiting Debrief</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Post-Mission Notes --}}
            @if($trip->driver_notes)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-chat-left-text-fill text-amber-500"></i> MISSION_DEBRIEFING
                    </h3>
                </div>
                <div class="p-6">
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/20 rounded-2xl text-xs font-bold text-amber-700 dark:text-amber-400 leading-relaxed italic">
                        "{{ $trip->driver_notes }}"
                    </div>
                </div>
            </div>
            @endif

            {{-- Emergency Override Protocol --}}
            @if(!$trip->isCompleted() && !in_array($trip->status, ['cancelled']))
            <div class="p-6 bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/20 rounded-3xl">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-xs font-black text-rose-600 uppercase tracking-tighter">PROTOCOL_INTERRUPTION_WARNING</h4>
                        <p class="text-[10px] font-bold text-rose-500/70 uppercase">Stopping this mission will terminate the current operational thread.</p>
                    </div>
                    <form action="{{ route('company.trips.cancel', $trip) }}" method="POST" onsubmit="return confirm('EXECUTE_TERMINATION_PROTOCOL?')">
                        @csrf @method('PATCH')
                        <button class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-rose-600/20">
                            ABORT_MISSION
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        {{-- Tactical Intelligence Sidebar --}}
        <div class="space-y-6">
            {{-- Linked Rental Intelligence --}}
            @if($trip->rental)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">RENTAL_LINK</div>
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ $trip->rental->rental_number ?? 'VOID_ID' }}</div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 dark:border-slate-800">
                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">CLIENT_IDENTITY</div>
                        <div class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase">{{ $trip->rental->customer?->name ?? 'ANONYMOUS_ENTITY' }}</div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">CONTRACT_STATE</span>
                        <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-500 text-[8px] font-black uppercase tracking-tighter ring-1 ring-blue-500/20">
                            {{ $trip->rental->status }}
                        </span>
                    </div>

                    <a href="{{ route('company.rentals.show', $trip->rental) }}" class="flex items-center justify-center gap-2 group w-full py-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-[9px] font-black text-blue-500 uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                        ACCESS_INTEL_STREAM
                        <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endif

            {{-- Vehicle Unit Intelligence --}}
            @if($trip->vehicle)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="bi bi-bus-front-fill"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">KINETIC_UNIT</div>
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ $trip->vehicle->vehicle_number }}</div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 dark:border-slate-800">
                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">MODEL_SPEC</div>
                        <div class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase leading-snug">{{ $trip->vehicle->name }}</div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">LATEST_TELEMTRY</span>
                        <span class="text-[10px] font-black text-emerald-500">{{ number_format($trip->vehicle->current_odometer ?? 0) }} KM</span>
                    </div>

                    <a href="{{ route('company.vehicles.show', $trip->vehicle) }}" class="flex items-center justify-center gap-2 group w-full py-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-[9px] font-black text-emerald-500 uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">
                        UNIT_DIAGNOSTICS
                        <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endif

            {{-- Operator Intelligence --}}
            @if($trip->driver)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">OPERATOR_IDENT</div>
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ $trip->driver->user?->name ?? 'UNKNOWN_PERSONNEL' }}</div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 dark:border-slate-800">
                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">COMPLIANCE_ID</div>
                        <div class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase leading-snug">{{ $trip->driver->license_number ?? 'MISSING_CREDENTIAL' }}</div>
                    </div>

                    <a href="{{ route('company.drivers.show', $trip->driver) }}" class="flex items-center justify-center gap-2 group w-full py-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-[9px] font-black text-amber-500 uppercase tracking-widest hover:bg-amber-600 hover:text-white transition-all">
                        OPERATOR_PROFILE
                        <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
