@extends('layouts.company')

@section('title', 'Fuel Log #' . $fuel->id)

@section('content')
<div class="space-y-6">
    {{-- Breadcrumb & Identity --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-[10px] font-black text-blue-500 uppercase tracking-widest">
                <a href="{{ route('company.fuel.index') }}" class="hover:text-blue-600 transition-colors">FUEL_LOG</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-400">ENTRY_DOSSIER_#{{ $fuel->id }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">FUEL_INTEL_DOSSIER</h1>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest ring-1 shadow-sm bg-emerald-500/10 text-emerald-500 ring-emerald-500/20">
                STATE: VALID_RECORD
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Dossier Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Refill Intelligence Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden text-slate-900 dark:text-white">
                <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-droplet-fill text-blue-500"></i> REFILL_TELEMETRY
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 text-slate-400">ENTRY_TIMESTAMP</div>
                            <div class="text-sm font-black">{{ $fuel->date->format('d M Y') }}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase mt-1">LOGGED_BY: {{ $fuel->creator->name ?? 'SYSTEM' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 text-slate-400">GEOLOCATION_STATION</div>
                            <div class="text-sm font-black">{{ $fuel->branch->name ?? 'CENTRAL_HUB' }}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase mt-1">OPERATIONAL_ZONE</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 text-slate-400">VOLUME</div>
                            <div class="text-lg font-black text-blue-600 dark:text-blue-400">{{ number_format($fuel->liters, 2) }} <span class="text-[10px]">L</span></div>
                        </div>
                        <div class="text-center p-4 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 text-slate-400">UNIT_PRICE</div>
                            <div class="text-lg font-black text-slate-900 dark:text-white">{{ number_format($fuel->cost_per_liter, 2) }}</div>
                        </div>
                        <div class="text-center p-4 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 text-slate-400">TOTAL_COST</div>
                            <div class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($fuel->total_amount, 2) }}</div>
                        </div>
                        <div class="text-center p-4 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 text-slate-400">CURRENCY</div>
                            <div class="text-lg font-black text-slate-400 uppercase">{{ auth()->user()->company->currency ?? 'AED' }}</div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-50 dark:border-slate-800">
                        <div class="flex items-center justify-between p-4 bg-slate-900 dark:bg-slate-950 rounded-2xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                                    <i class="bi bi-speedometer2"></i>
                                </div>
                                <div>
                                    <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest">ODOMETER_AT_REFILL</div>
                                    <div class="text-lg font-black text-white">{{ number_format($fuel->odometer_reading) }} <span class="text-xs text-slate-500 uppercase">KM</span></div>
                                </div>
                            </div>
                            <i class="bi bi-activity text-2xl text-blue-500 opacity-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit Section --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 flex items-center justify-between text-slate-900 dark:text-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                        <i class="bi bi-shield-check text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-tight">ENTITY_INTEGRITY_VERIFIED</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Record created on {{ $fuel->created_at->format('d M Y @ H:i') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <form action="{{ route('company.fuel.destroy', $fuel) }}" method="POST" onsubmit="return confirm('INITIATE_DELETION_PROTOCOL?')">
                        @csrf @method('DELETE')
                        <button class="text-rose-500 hover:text-rose-600 text-[10px] font-black uppercase tracking-widest transition-colors">
                            DELETE_RECORD
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            {{-- Vehicle Unit --}}
            @if($fuel->vehicle)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden text-slate-900 dark:text-white">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="bi bi-bus-front-fill"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-slate-400">KINETIC_UNIT</div>
                            <div class="text-xs font-black uppercase">{{ $fuel->vehicle->vehicle_number }}</div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 dark:border-slate-800">
                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 text-slate-400">UNIT_IDENTITY</div>
                        <div class="text-xs font-black uppercase">{{ $fuel->vehicle->name }}</div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-slate-400">LATEST_TELEMETRY</span>
                        <span class="text-xs font-black text-emerald-500">{{ number_format($fuel->vehicle->current_odometer ?? 0) }} KM</span>
                    </div>

                    <a href="{{ route('company.vehicles.show', $fuel->vehicle) }}" class="flex items-center justify-center gap-2 group w-full py-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-[9px] font-black text-blue-500 uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                        VIEW_UNIT_PORTFOLIO
                        <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endif

            {{-- Vendor / Station --}}
            @if($fuel->vendor)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden text-slate-900 dark:text-white">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-slate-400">ENERGY_VENDOR</div>
                            <div class="text-xs font-black uppercase">{{ $fuel->vendor->name }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Quick Stats --}}
            <div class="p-6 bg-slate-900 dark:bg-slate-950 rounded-3xl border border-slate-800 text-white">
                <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">REFILL_CONTEXT</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Liters Filled</span>
                        <span class="text-xs font-black">{{ number_format($fuel->liters, 1) }} L</span>
                    </div>
                    <div class="flex justify-between items-center text-emerald-500">
                        <span class="text-[10px] font-bold uppercase">Net Expenditure</span>
                        <span class="text-xs font-black">{{ number_format($fuel->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
