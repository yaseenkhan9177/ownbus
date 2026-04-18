@extends('layouts.company')

@section('title', 'Fleet Control Center')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Fleet Operations Control</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700" x-data="{ revView: 'monthly', utilView: 'trend' }">

    {{-- ═══════════════════════════════════════════════
         VIEW TOGGLES (Phase 4 Component)
    ═══════════════════════════════════════════════ --}}
    <div class="flex justify-end mb-2">
        <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 rounded-lg shadow-sm" id="view-toggle-container">
            <button id="btn-exec-view" class="px-4 py-1.5 text-xs font-black uppercase tracking-widest rounded-md bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm transition-all border border-slate-200 dark:border-slate-800">Executive View</button>
            <button id="btn-ops-view" class="px-4 py-1.5 text-xs font-black uppercase tracking-widest rounded-md text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-all border border-transparent">Operations View</button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         QUICK ACTIONS (ERP Phase)
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-2">
        <a href="{{ route('company.fleet.create') }}" class="flex flex-col items-center p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-blue-500/50 transition-all group">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-blue-600 transition-colors">Add Vehicle</span>
        </a>
        <a href="{{ route('company.rentals.create') }}" class="flex flex-col items-center p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-emerald-500/50 transition-all group">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002 2h2a2 2 0 012-2" />
                </svg>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-emerald-600 transition-colors">Create Rental</span>
        </a>
        <a href="{{ route('company.customers.create') }}" class="flex flex-col items-center p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-500/50 transition-all group">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-indigo-600 transition-colors">Add Customer</span>
        </a>
        <a href="{{ route('company.finance.invoices') }}" class="flex flex-col items-center p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-amber-500/50 transition-all group">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-amber-600 transition-colors">Record Payment</span>
        </a>
        <a href="{{ route('company.kanban.index') }}" class="flex flex-col items-center p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-purple-500/50 transition-all group col-span-2 md:col-span-1">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-purple-600 transition-colors">Assign Driver</span>
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════
         ZONE 1: CEO Snapshot Bar
    ═══════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-4 shadow-lg mb-6">
        <div class="absolute inset-0 bg-white/5 backdrop-blur-sm"></div>
        <div class="relative flex flex-wrap lg:flex-nowrap items-center justify-between gap-4">

            {{-- Fleet Status --}}
            <div class="flex items-center space-x-3 w-[47%] sm:w-[31%] xl:w-auto">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/20 flex items-center justify-center text-cyan-400 border border-cyan-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Fleet Status</p>
                    <p class="text-sm font-black text-white">{{ $data['kpis']['active_rentals'] }} / {{ $data['kpis']['total_vehicles'] }} <span class="text-slate-500 text-xs font-medium">Active</span></p>
                </div>
            </div>

            <div class="hidden lg:block w-px h-8 bg-slate-700"></div>

            {{-- Active Revenue --}}
            <div class="flex items-center space-x-3 w-[47%] sm:w-[31%] xl:w-auto">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">MTD Revenue</p>
                    <p class="text-sm font-black text-emerald-400">AED {{ number_format($data['kpis']['revenue_this_month'], 0) }}</p>
                </div>
            </div>

            <div class="hidden lg:block w-px h-8 bg-slate-700"></div>

            {{-- Utilization --}}
            <div class="flex items-center space-x-3 w-[47%] sm:w-[31%] xl:w-auto">
                <div class="relative w-10 h-10 flex items-center justify-center">
                    <svg class="w-10 h-10 transform -rotate-90">
                        <circle cx="20" cy="20" r="16" stroke="currentColor" stroke-width="3" fill="transparent" class="text-slate-700" />
                        <circle cx="20" cy="20" r="16" stroke="currentColor" stroke-width="3" fill="transparent"
                            stroke-dasharray="100"
                            stroke-dashoffset="{{ 100 - $data['charts']['fleet_utilization'] }}"
                            class="text-blue-500 transition-all duration-1000" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-[10px] font-black text-white">{{ $data['charts']['fleet_utilization'] }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Utilization</p>
                    <p class="text-sm font-black text-white">{{ $data['charts']['fleet_utilization'] }}% <span class="text-slate-500 text-xs font-medium">Deployed</span></p>
                </div>
            </div>

            <div class="hidden lg:block w-px h-8 bg-slate-700"></div>

            {{-- Risk Index --}}
            <div class="flex items-center space-x-3 w-[47%] sm:w-[31%] xl:w-auto">
                @php
                $riskScore = $data['risk_score']['score'] ?? 0;
                $riskColor = $riskScore < 50 ? 'emerald' : ($riskScore < 75 ? 'amber' : 'rose' );
                    $riskLabel=$riskScore < 50 ? 'LOW' : ($riskScore < 75 ? 'MEDIUM' : 'HIGH' );
                    @endphp
                    <div class="w-10 h-10 rounded-xl bg-{{ $riskColor }}-500/20 flex items-center justify-center text-{{ $riskColor }}-400 border border-{{ $riskColor }}-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Risk Index</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-xs font-black text-white">{{ $riskLabel }}</span>
                    <div class="w-2 h-2 rounded-full bg-{{ $riskColor }}-500 {{ $riskColor === 'rose' ? 'animate-pulse' : '' }}"></div>
                </div>
            </div>
        </div>

        <div class="hidden lg:block w-px h-8 bg-slate-700"></div>

        {{-- Cash Position (VAT) --}}
        <div class="flex items-center space-x-3 w-[47%] sm:w-[31%] xl:w-auto">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">VAT Payable</p>
                <p class="text-sm font-black text-white">AED {{ number_format($data['vat_summary']['net_vat_payable'] ?? 0, 0) }}</p>
            </div>
        </div>

        <div class="hidden lg:block w-px h-8 bg-slate-700"></div>

        {{-- Vehicles in Maintenance --}}
        <div class="flex items-center space-x-3 w-[47%] sm:w-[31%] xl:w-auto">
            <div class="w-10 h-10 rounded-xl bg-orange-500/20 flex items-center justify-center text-orange-400 border border-orange-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">In Maintenance</p>
                <p class="text-sm font-black text-white">{{ $data['kpis']['vehicles_in_maintenance'] }} <span class="text-slate-500 text-xs font-medium">Buses</span></p>
            </div>
        </div>

        <div class="hidden lg:block w-px h-8 bg-slate-700"></div>

        {{-- Outstanding Payments --}}
        <div class="flex items-center space-x-3 w-[47%] sm:w-[31%] xl:w-auto">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-400 border border-amber-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Outstanding</p>
                <p class="text-sm font-black text-amber-400">AED {{ number_format($data['kpis']['outstanding_payments'], 0) }}</p>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════
         ZONE 2: Attention Wall & Health Gauge
    ═══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Left: Attention Wall (col-span-2) --}}
    <div class="lg:col-span-2">
        @php
        $hasCriticalRisks = collect($data['risks'])->where('severity', 'high')->count() > 0;
        $criticalRisksCount = collect($data['risks'])->where('severity', 'high')->count();
        @endphp

        @if($hasCriticalRisks)
        <div class="bg-gradient-to-br from-rose-900 via-rose-800 to-rose-950 border border-rose-500/30 rounded-2xl p-6 shadow-lg shadow-rose-900/20 relative overflow-hidden h-full flex items-center justify-between group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/10 blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-3 h-3 rounded-full bg-rose-500 animate-pulse"></div>
                    <h2 class="text-xl font-black text-white uppercase tracking-tight">Immediate Action Required</h2>
                </div>
                <p class="text-rose-200 text-sm font-semibold">{{ $criticalRisksCount }} Critical System Risks Detected</p>
            </div>
            <button onclick="document.getElementById('riskCenterModal').showModal()" class="px-5 py-2.5 bg-rose-500 hover:bg-rose-400 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm cursor-pointer z-10 relative">
                View All &rarr;
            </button>
            {{-- Embed the existing risk center component as a hidden dialog for later, or just show it inline below? The user requested 'View All ->' link. --}}
        </div>
        @else
        <div class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-emerald-950 border border-emerald-500/30 rounded-2xl p-6 shadow-lg shadow-emerald-900/20 relative overflow-hidden h-full flex items-center justify-between">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_10px_theme('colors.emerald.500')]"></div>
                    <h2 class="text-xl font-black text-white uppercase tracking-tight">System Stable</h2>
                </div>
                <p class="text-emerald-200 text-sm font-medium">No Critical Risks. Operations running efficiently.</p>
            </div>
            {{-- Still provide access to non-critical risks --}}
            <button onclick="document.getElementById('riskCenterModal').showModal()" class="px-5 py-2.5 bg-emerald-800 hover:bg-emerald-700 text-emerald-100 text-xs font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm border border-emerald-600/50 cursor-pointer z-10 relative">
                View Center &rarr;
            </button>
        </div>
        @endif
    </div>

    {{-- Right: Company Health Gauge --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-md flex items-center justify-center relative overflow-hidden h-full">
        @php
        $efficiencyScore = $data['efficiency']['score'] ?? 80;
        $riskScoreVal = $data['risk_score']['score'] ?? 20;
        $healthScore = max(0, min(100, round(($efficiencyScore + (100 - $riskScoreVal)) / 2)));

        $healthColor = $healthScore >= 75 ? 'emerald' : ($healthScore >= 50 ? 'amber' : 'rose');
        $healthStatus = $healthScore >= 75 ? 'Healthy' : ($healthScore >= 50 ? 'Monitor' : 'Critical');
        @endphp

        <div class="text-center w-full">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Company Health Matrix</p>
            <div class="relative w-32 h-32 mx-auto">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                    <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" fill="transparent"
                        stroke-dasharray="251.2"
                        stroke-dashoffset="{{ 251.2 - (251.2 * $healthScore / 100) }}"
                        stroke-linecap="round"
                        class="text-{{ $healthColor }}-500 transition-all duration-1000" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center pt-2">
                    <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $healthScore }}</span>
                </div>
            </div>
            <div class="flex items-center justify-center gap-1.5 mt-2">
                <div class="w-2 h-2 rounded-full bg-{{ $healthColor }}-500"></div>
                <span class="text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-widest">{{ $healthStatus }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Risk Center Modal --}}
<dialog id="riskCenterModal" class="backdrop:bg-slate-900/80 bg-transparent rounded-2xl w-full max-w-4xl p-0 m-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-2xl">
        <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-slate-800">
            <h3 class="font-black text-slate-900 dark:text-white uppercase">Risk Control Center</h3>
            <form method="dialog">
                <button class="text-slate-500 hover:text-slate-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </form>
        </div>
        <div class="p-6 max-h-[80vh] overflow-y-auto">
            @include('company.dashboard.partials.risk-center', ['risks' => $data['risks']])
        </div>
    </div>
</dialog>

{{-- ═══════════════════════════════════════════════
         DOCUMENT EXPIRY TRACKER (Mulkiya / Insurance / Permit)
    ═══════════════════════════════════════════════ --}}
<div class="mb-6">
    <div class="flex items-center space-x-2 mb-4 mt-8">
        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Document Expiry Tracker</h2>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-[10px] font-black text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800">
                        <th class="py-3 px-6">Vehicle</th>
                        <th class="py-3 px-6 text-center">Registration (Mulkiya)</th>
                        <th class="py-3 px-6 text-center">Insurance</th>
                        <th class="py-3 px-6 text-center">Inspection</th>
                        <th class="py-3 px-6 text-center">Route Permit</th>
                        <th class="py-3 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @forelse($data['expiring_vehicles'] as $vehicle)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-black uppercase text-slate-600 dark:text-slate-300">
                                    {{ substr($vehicle->vehicle_number, -4) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $vehicle->name }}</p>
                                    <p class="text-[9px] text-slate-500 uppercase">{{ $vehicle->vehicle_number }}</p>
                                </div>
                            </div>
                        </td>

                        @php
                        // Helper function to format date and apply color rules
                        $formatDoc = function($date) {
                        if (!$date) return '<span class="text-slate-400">Not Set</span>';

                        $carbonDate = \Carbon\Carbon::parse($date);
                        $daysLeft = now()->diffInDays($carbonDate, false);

                        $colorClass = 'text-slate-600 dark:text-slate-300';
                        $bgClass = 'bg-slate-100 dark:bg-slate-800';
                        $pulse = '';

                        if ($daysLeft < 0) {
                            // Expired=Red
                            $colorClass='text-rose-600 dark:text-rose-400' ;
                            $bgClass='bg-rose-100 dark:bg-rose-900/40 border border-rose-200 dark:border-rose-800/50' ;
                            $pulse='animate-pulse' ;
                            } elseif ($daysLeft <=7) {
                            // <=7 days=Orange
                            $colorClass='text-orange-600 dark:text-orange-400' ;
                            $bgClass='bg-orange-100 dark:bg-orange-900/40 border border-orange-200 dark:border-orange-800/50' ;
                            } elseif ($daysLeft <=30) {
                            // <=30 days=Yellow
                            $colorClass='text-amber-600 dark:text-amber-400' ;
                            $bgClass='bg-amber-100 dark:bg-amber-900/40 border border-amber-200 dark:border-amber-800/50' ;
                            }

                            return sprintf( '<span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase whitespace-nowrap %s %s %s">%s</span>' ,
                            $bgClass, $colorClass, $pulse, $carbonDate->format('d M Y')
                            );
                            };
                            @endphp

                            <td class="py-4 px-6 text-center">{!! $formatDoc($vehicle->registration_expiry) !!}</td>
                            <td class="py-4 px-6 text-center">{!! $formatDoc($vehicle->insurance_expiry) !!}</td>
                            <td class="py-4 px-6 text-center">{!! $formatDoc($vehicle->inspection_expiry_date) !!}</td>
                            <td class="py-4 px-6 text-center">{!! $formatDoc($vehicle->route_permit_expiry) !!}</td>

                            <td class="py-4 px-6 text-right">
                                <a href="{{ url('/company/fleet/' . $vehicle->id . '/edit') }}" class="text-[10px] font-black text-blue-500 uppercase hover:text-blue-600 transition-colors">Update &rarr;</a>
                            </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-600 dark:text-slate-400">All documents are up to date! 🎉</p>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1">No expiries within 30 days.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
         ZONE 3: Live Operations Hub
    ═══════════════════════════════════════════════ --}}
<div class="flex items-center space-x-2 mb-4 mt-8">
    <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Live Operations Hub</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Left: Mini Fleet Map (col-span-2) --}}
    <div id="zone3-map-container" class="lg:col-span-2 h-[500px] flex flex-col bg-slate-900 border border-slate-800 rounded-2xl shadow-md overflow-hidden relative transition-all duration-500">
        <x-dashboard.fleet-map :companyId="$company->id" />
    </div>

    {{-- Right: Active Deployments & Timeline --}}
    <div class="space-y-6 h-[500px] flex flex-col ops-detail transition-all duration-500 overflow-hidden">

        {{-- Active Deployments (Slim version) --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-md p-4 flex-1 flex flex-col min-h-0">
            <div class="flex items-center justify-between mb-3 shrink-0">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Active Jobs</h3>
                <a href="{{ route('company.rentals.index') }}" class="text-[10px] font-bold text-cyan-500 uppercase hover:text-cyan-400">View All</a>
            </div>
            <div class="overflow-y-auto pr-2 space-y-3 flex-1 custom-scrollbar">
                @forelse($data['active_rentals'] as $rental)
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border border-transparent hover:border-slate-100 dark:hover:border-slate-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded bg-cyan-500/10 flex items-center justify-center text-[10px] font-black text-cyan-500 uppercase">
                            {{ substr($rental->vehicle->registration_no ?? 'BUS', -3) }}
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-900 dark:text-slate-100 truncate max-w-[120px]">{{ $rental->customer->name ?? 'N/A' }}</p>
                            <p class="text-[9px] text-slate-500">{{ $rental->vehicle->name ?? 'N/A' }} &mdash; {{ $rental->start_date->format('d M') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="w-2 h-2 rounded-full inline-block {{ $rental->status === 'active' ? 'bg-emerald-500 shadow-[0_0_5px_theme(\'colors.emerald.500\')]' : 'bg-rose-500 animate-pulse' }}"></span>
                    </div>
                </div>
                @empty
                <p class="text-[10px] text-slate-500 text-center py-4">No active deployments.</p>
                @endforelse
            </div>
        </div>

        {{-- Operational Timeline (Slim version) --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-md p-4 flex-1 flex flex-col min-h-0">
            <div class="flex items-center justify-between mb-3 shrink-0">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Live Event Stream</h3>
            </div>
            <div class="overflow-y-auto pr-1 flex-1 custom-scrollbar w-full">
                <div class="space-y-4 relative before:absolute before:inset-0 before:ml-2 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 dark:before:via-slate-700 before:to-transparent ml-2">
                    @forelse(collect($data['timeline'])->take(8) as $event)
                    @php
                    $iconClass = 'bi-record-circle-fill';
                    $colorClass = 'text-cyan-500 bg-cyan-50 dark:bg-cyan-500/10 border-cyan-200 dark:border-cyan-500/30';
                    $actionLower = strtolower($event->action);

                    if (str_contains($actionLower, 'created') || str_contains($actionLower, 'added') || str_contains($actionLower, 'new')) {
                    $iconClass = 'bi-plus-circle-fill';
                    $colorClass = 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/30';
                    } elseif (str_contains($actionLower, 'updated') || str_contains($actionLower, 'modified') || str_contains($actionLower, 'edit')) {
                    $iconClass = 'bi-pencil-fill';
                    $colorClass = 'text-blue-500 bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/30';
                    } elseif (str_contains($actionLower, 'deleted') || str_contains($actionLower, 'removed') || str_contains($actionLower, 'terminated')) {
                    $iconClass = 'bi-trash-fill';
                    $colorClass = 'text-rose-500 bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/30';
                    } elseif (str_contains($actionLower, 'payment') || str_contains($actionLower, 'invoice') || str_contains($actionLower, 'paid')) {
                    $iconClass = 'bi-currency-dollar';
                    $colorClass = 'text-amber-500 bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/30';
                    } elseif (str_contains($actionLower, 'active') || str_contains($actionLower, 'started')) {
                    $iconClass = 'bi-play-circle-fill';
                    $colorClass = 'text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 border-indigo-200 dark:border-indigo-500/30';
                    }
                    @endphp
                    <div class="relative flex items-center group pb-2">
                        <div class="flex items-center justify-center w-6 h-6 rounded-full border {{ $colorClass }} shrink-0 z-10 -ml-[11px] ring-4 ring-white dark:ring-slate-900">
                            <i class="bi {{ $iconClass }} text-[10px]"></i>
                        </div>
                        <div class="w-full ml-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 group-hover:border-cyan-200 dark:group-hover:border-cyan-500/30 transition-all">
                            <div class="flex items-center justify-between space-x-2 mb-1">
                                <div class="font-black text-slate-900 dark:text-slate-100 text-[10px] uppercase tracking-widest truncate">{{ $event->action }}</div>
                                <time class="font-bold text-slate-400 text-[9px] uppercase whitespace-nowrap">{{ \Carbon\Carbon::parse($event->occurred_at)->diffForHumans(null, true, true) }}</time>
                            </div>
                            <div class="text-slate-500 text-[10px] leading-relaxed line-clamp-2" title="{{ $event->details }}">{{ $event->details }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-[10px] text-slate-500 text-center py-4 relative z-10 bg-white dark:bg-slate-900">System quiet. No recent events.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════
         ZONE 4: Financial Command Grid
    ═══════════════════════════════════════════════ --}}
<div class="flex items-center space-x-2 mb-4 mt-8">
    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Financial Command Grid</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Column 1: Revenue & Margin --}}
    <div class="space-y-6">
        {{-- Revenue Trend Chart --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-md transition-shadow hover:shadow-lg" x-data="{ revView: 'monthly' }">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Revenue Velocity</h3>
                    <div class="flex items-center gap-3">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="revView === 'monthly' ? 'Monthly Trend' : 'Daily Trend'">Monthly Trend</p>
                        <div class="flex bg-slate-100 dark:bg-slate-800 rounded-lg p-1">
                            <button @click="revView = 'daily'; switchRevenueView('daily')" :class="revView === 'daily' ? 'bg-white dark:bg-slate-700 shadow-sm text-slate-900 dark:text-white' : 'text-slate-500'" class="px-3 py-1 text-[10px] font-black uppercase rounded-md transition-all">Daily</button>
                            <button @click="revView = 'monthly'; switchRevenueView('monthly')" :class="revView === 'monthly' ? 'bg-white dark:bg-slate-700 shadow-sm text-slate-900 dark:text-white' : 'text-slate-500'" class="px-3 py-1 text-[10px] font-black uppercase rounded-md transition-all">Monthly</button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full h-fit">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span>Stable</span>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        {{-- Automated Billing --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-md">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Daily Billing Engine</h3>
            <div class="flex justify-between items-center p-4 bg-cyan-50 dark:bg-cyan-950/20 rounded-xl border border-cyan-100 dark:border-cyan-900/30">
                <div>
                    <p class="text-2xl font-black text-cyan-600">AED {{ number_format($data['billing_today']['revenue_generated'] ?? 0, 0) }}</p>
                    <p class="text-[10px] text-slate-500 mt-1">{{ $data['billing_today']['contracts_billed'] ?? 0 }} Contracts Auto-Billed Today</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-cyan-500/10 flex items-center justify-center text-cyan-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Column 2: Risk & Compliance --}}
    <div class="space-y-6">
        {{-- Overdue AR & Collections --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-md {{ $data['credit_blocked_count'] > 0 ? 'ring-1 ring-rose-500/50 shadow-rose-900/10' : '' }}">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Cash Leakage Risk (AR)</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-xl {{ $data['kpis']['overdue_rentals'] > 0 ? 'bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30' : 'bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700' }} relative overflow-hidden group">
                    <div class="absolute inset-0 bg-rose-500/0 {{ $data['kpis']['overdue_rentals'] > 0 ? 'group-hover:bg-rose-500/5' : '' }} transition-colors"></div>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1 flex items-center gap-1.5 relative z-10">
                        Overdue Accounts
                        @if($data['kpis']['overdue_rentals'] > 0)
                        <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span></span>
                        @endif
                    </p>
                    <p class="text-2xl font-black relative z-10 {{ $data['kpis']['overdue_rentals'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">{{ $data['kpis']['overdue_rentals'] }}</p>
                </div>

                <div class="p-4 rounded-xl {{ $data['credit_blocked_count'] > 0 ? 'bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30' : 'bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700' }} relative overflow-hidden group">
                    <div class="absolute inset-0 bg-amber-500/0 {{ $data['credit_blocked_count'] > 0 ? 'group-hover:bg-amber-500/5' : '' }} transition-colors"></div>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1 relative z-10">Credit Blocked</p>
                    <p class="text-2xl font-black relative z-10 {{ $data['credit_blocked_count'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' }}">{{ $data['credit_blocked_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            {{-- Fine Recovery --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-md">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Fine Recovery</h3>
                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">AED {{ number_format($data['fine_recovery']['recovered_this_month'], 0) }}</p>
                <p class="text-[10px] font-bold mt-1 text-slate-500 uppercase flex items-center gap-1">
                    Success <span class="{{ $data['fine_recovery']['recovery_rate_pct'] < 80 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $data['fine_recovery']['recovery_rate_pct'] }}%</span>
                </p>
            </div>

            {{-- VAT Payable --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-md">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">VAT Payable</h3>
                <p class="text-2xl font-black text-slate-900 dark:text-white">AED {{ number_format($data['vat_summary']['net_vat_payable'], 0) }}</p>
                <p class="text-[10px] font-bold mt-1 text-slate-500 uppercase">{{ $data['vat_summary']['quarter_label'] }}</p>
            </div>
        </div>

        {{-- Offline Fleet Context --}}
        @if(count($data['offline_vehicles']) > 0)
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 rounded-2xl p-5 shadow-sm">
            <h3 class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
                GPS Connectivity Alert
            </h3>
            <div class="space-y-2">
                @foreach(array_slice($data['offline_vehicles'], 0, 3) as $vehicle)
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-700 dark:text-rose-400">{{ $vehicle['vehicle_number'] }}</span>
                    <span class="text-[10px] text-rose-500 bg-rose-100 dark:bg-rose-900/50 px-2 py-0.5 rounded font-mono">{{ $vehicle['last_seen'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════
         ZONE 5: Predictive Intelligence Layer
    ═══════════════════════════════════════════════ --}}
<div class="flex items-center space-x-3 mb-4 mt-8">
    <div class="p-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20">
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
        </svg>
    </div>
    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight flex items-center gap-2">Predictive Intelligence <span class="px-2 py-0.5 rounded bg-indigo-500 text-white text-[9px] font-black uppercase tracking-widest">AI Powered</span></h2>
</div>

{{-- ═══════════════════════════════════════════════
     1️⃣ AI RISK SUMMARY STRIP
═══════════════════════════════════════════════ --}}
<div class="relative overflow-hidden bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-6 group hover:shadow-md transition-all">
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-cyan-50/30 dark:via-cyan-900/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>

    <div class="relative z-10 flex-1">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">AI Operational Risk Snapshot</h3>
        <p class="text-xl font-bold text-slate-900 dark:text-white">System Stability Analysis</p>
    </div>

    <div class="relative z-10 flex items-center space-x-8">
        {{-- Breakdown Risk Dial --}}
        <div class="flex items-center space-x-3">
            <div class="relative w-14 h-14 flex items-center justify-center">
                <svg class="w-14 h-14 transform -rotate-90">
                    <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                    <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent"
                        stroke-dasharray="150"
                        stroke-dashoffset="{{ 150 - (150 * ($data['kpis']['avg_risk_index'] ?? 5) / 100) }}"
                        class="text-emerald-500 transition-all duration-1000" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm font-black text-slate-900 dark:text-white">{{ $data['kpis']['avg_risk_index'] ?? 0 }}</span>
                </div>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Breakdown Risk</p>
                <p class="text-xs font-black text-emerald-500 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Low</p>
            </div>
        </div>

        <div class="w-px h-10 bg-slate-200 dark:bg-slate-700"></div>

        {{-- Driver Safety Dial --}}
        <div class="flex items-center space-x-3">
            <div class="relative w-14 h-14 flex items-center justify-center">
                <svg class="w-14 h-14 transform -rotate-90">
                    <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                    <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent"
                        stroke-dasharray="150"
                        stroke-dashoffset="{{ 150 - (150 * ($data['kpis']['avg_safe_score'] ?? 95) / 100) }}"
                        class="text-emerald-500 transition-all duration-1000" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm font-black text-slate-900 dark:text-white">{{ $data['kpis']['avg_safe_score'] ?? 100 }}</span>
                </div>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Driver Safety</p>
                <p class="text-xs font-black text-emerald-500 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Excellent</p>
            </div>
        </div>

        <div class="w-px h-10 bg-slate-200 dark:bg-slate-700"></div>

        <div class="text-right">
            <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">Trend (6M)</p>
            <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest">Stable</span>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     2️⃣ BREAKDOWN & ACCIDENT CARDS (Side by Side)
═══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    {{-- Card 1: Vehicle Breakdown Risk --}}
    @php
    $highRiskVehiclesCount = collect($data['predictive_maintenance']['predictions'])->where('risk_level', 'high')->count();
    @endphp
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 dark:from-slate-900 dark:to-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group hover:border-rose-400/50 dark:hover:border-rose-500/30 transition-all duration-700">
        {{-- Background Glow --}}
        <div class="absolute top-0 right-0 w-48 h-48 bg-rose-500/10 dark:bg-rose-500/10 blur-3xl -mr-20 -mt-20 pointer-events-none transition-transform group-hover:scale-150 duration-700"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-rose-500/5 blur-2xl -ml-16 -mb-16 pointer-events-none"></div>

        <div class="flex justify-between items-start mb-6 relative z-10">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Vehicle Breakdown</h3>
                    <p class="text-[9px] text-slate-500 uppercase tracking-wider mt-0.5">Probability Engine</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">Predictive</span>
        </div>

        <div class="flex flex-col items-center justify-center py-6 relative z-10">
            <p class="text-7xl font-black tracking-tighter leading-none {{ $highRiskVehiclesCount > 0 ? 'text-rose-500 drop-shadow-[0_0_25px_rgba(225,29,72,0.4)] dark:drop-shadow-[0_0_25px_rgba(244,63,94,0.3)]' : 'text-slate-300 dark:text-white' }}">{{ $highRiskVehiclesCount }}</p>
            <p class="text-[10px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Units At Critical Risk</p>
        </div>

        <div class="mt-auto pt-5 border-t border-slate-100 dark:border-slate-800/80 relative z-10">
            @if($highRiskVehiclesCount > 0)
            <div class="flex items-center justify-between">
                <p class="text-xs font-black text-rose-600 dark:text-rose-400 flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span></span>
                    High Probability Detected
                </p>
                <button class="text-[9px] font-black text-slate-400 hover:text-rose-500 uppercase tracking-widest transition-colors">View Fleet &rarr;</button>
            </div>
            @else
            <div class="flex items-center justify-between">
                <p class="text-xs font-black text-emerald-500 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_theme('colors.emerald.500')]"></span>
                    No High Risk Units
                </p>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">All Clear</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Card 2: Driver Accident Risk --}}
    @php
    $highRiskDriversCount = collect($data['driver_risk']['recent_snapshots'])->where('risk_level', 'high')->count();
    @endphp
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 dark:from-slate-900 dark:to-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group hover:border-amber-400/50 dark:hover:border-amber-500/30 transition-all duration-700">
        {{-- Background Glow --}}
        <div class="absolute top-0 right-0 w-48 h-48 bg-amber-500/10 dark:bg-amber-500/10 blur-3xl -mr-20 -mt-20 pointer-events-none transition-transform group-hover:scale-150 duration-700"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-amber-500/5 blur-2xl -ml-16 -mb-16 pointer-events-none"></div>

        <div class="flex justify-between items-start mb-6 relative z-10">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Driver Accident Risk</h3>
                    <p class="text-[9px] text-slate-500 uppercase tracking-wider mt-0.5">Behavioral Model</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">Active</span>
        </div>

        <div class="flex flex-col items-center justify-center py-6 relative z-10">
            <p class="text-7xl font-black tracking-tighter leading-none {{ $highRiskDriversCount > 0 ? 'text-amber-500 drop-shadow-[0_0_25px_rgba(217,119,6,0.4)] dark:drop-shadow-[0_0_25px_rgba(245,158,11,0.3)]' : 'text-slate-300 dark:text-white' }}">{{ $highRiskDriversCount }}</p>
            <p class="text-[10px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Drivers At Risk</p>
        </div>

        <div class="mt-auto pt-5 border-t border-slate-100 dark:border-slate-800/80 relative z-10">
            @if($highRiskDriversCount > 0)
            <div class="flex items-center justify-between">
                <p class="text-xs font-black text-amber-600 dark:text-amber-400 flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span></span>
                    Immediate Coaching Needed
                </p>
                <button class="text-[9px] font-black text-slate-400 hover:text-amber-500 uppercase tracking-widest transition-colors">Action List &rarr;</button>
            </div>
            @else
            <div class="flex items-center justify-between">
                <p class="text-xs font-black text-emerald-500 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_theme('colors.emerald.500')]"></span>
                    No High Risk Drivers
                </p>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">All Clear</span>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Phase 7H: Driver Risk Monitor --}}
<div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">👤 Driver Risk Monitor</h3>
            <p class="text-xs text-slate-500 mt-1">Safety scoring (0-100) based on speeding, harsh driving, fines, and compliance</p>
        </div>
        <div class="flex items-center space-x-6">
            <div class="text-center">
                <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">High Risk</p>
                <p class="text-lg font-black text-rose-600">{{ $data['driver_risk']['high_risk_count'] }}</p>
            </div>
            <div class="text-center border-l border-slate-100 dark:border-slate-800 pl-6">
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Medium Risk</p>
                <p class="text-lg font-black text-amber-600">{{ $data['driver_risk']['medium_risk_count'] }}</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto ops-detail transition-all duration-500 opacity-100">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800">
                    <th class="pb-3 px-2">Driver</th>
                    <th class="pb-3 px-2">Safety Score</th>
                    <th class="pb-3 px-2">Risk Level</th>
                    <th class="pb-3 px-2">Violations (30d)</th>
                    <th class="pb-3 px-2 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                @forelse($data['driver_risk']['recent_snapshots'] as $snapshot)
                @php
                $breakdown = json_decode($snapshot->breakdown_json, true);
                @endphp
                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="py-4 px-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-black uppercase">
                                {{ substr($snapshot->first_name, 0, 1) }}{{ substr($snapshot->last_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $snapshot->first_name }} {{ $snapshot->last_name }}</p>
                                <p class="text-[10px] text-slate-500">ID: #{{ $snapshot->driver_id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-2">
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 h-1.5 w-16 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full {{ $snapshot->score >= 80 ? 'bg-emerald-500' : ($snapshot->score >= 60 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $snapshot->score }}%;"></div>
                            </div>
                            <span class="text-xs font-black">{{ $snapshot->score }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter
                                {{ $snapshot->risk_level === 'low' ? 'bg-emerald-500/10 text-emerald-500' : 
                                   ($snapshot->risk_level === 'medium' ? 'bg-amber-500/10 text-amber-500' : 'bg-rose-500/10 text-rose-500 animate-pulse') }}">
                            {{ $snapshot->risk_level }} risk
                        </span>
                    </td>
                    <td class="py-4 px-2">
                        <div class="flex items-center space-x-3 text-[10px]">
                            <span title="Speeding" class="flex items-center text-slate-500"><i class="w-3 h-3 mr-1 text-rose-400">⚡</i> {{ $breakdown['speed'] < 100 ? 'Alerts' : 'Clean' }}</span>
                            <span title="Harsh Driving" class="flex items-center text-slate-500"><i class="w-3 h-3 mr-1 text-amber-400">🚗</i> {{ $breakdown['harsh'] < 100 ? 'Harsh' : 'Safe' }}</span>
                            <span title="Compliance" class="flex items-center text-slate-500"><i class="w-3 h-3 mr-1 text-cyan-400">📋</i> {{ $breakdown['compliance'] }}%</span>
                        </div>
                    </td>
                    <td class="py-4 px-2 text-right">
                        <a href="{{ url('/portal/drivers/' . $snapshot->driver_id) }}" class="text-[10px] font-black text-cyan-500 uppercase hover:underline">Review &rarr;</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-sm text-slate-500">No risk profiling data available. System will calculate nightly.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Phase 7I: Fleet Replacement Intelligence --}}
<div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">🚍 Fleet Replacement Intelligence</h3>
            <p class="text-xs text-slate-500 mt-1">Lifecycle analysis based on margin decline, maintenance escalation, and reliability</p>
        </div>
        <div class="flex items-center space-x-12">
            <div class="text-center">
                <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Replace Soon</p>
                <p class="text-xl font-black text-rose-600">{{ $data['fleet_replacement']['replace_count'] }}</p>
            </div>
            <div class="text-center border-l border-slate-100 dark:border-slate-800 pl-8">
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Under Review</p>
                <p class="text-xl font-black text-amber-600">{{ $data['fleet_replacement']['monitor_count'] }}</p>
            </div>
            <div class="text-center border-l border-slate-100 dark:border-slate-800 pl-8">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fleet Avg Score</p>
                <p class="text-xl font-black text-slate-900 dark:text-white">{{ $data['fleet_replacement']['avg_fleet_score'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800">
                        <th class="pb-3 px-2">Vehicle</th>
                        <th class="pb-3 px-2">Age / Odo</th>
                        <th class="pb-3 px-2">R-Score</th>
                        <th class="pb-3 px-2">Recommendation</th>
                        <th class="pb-3 px-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @forelse($data['fleet_replacement']['top_replacement_candidates'] as $candidate)
                    @php
                    $signals = json_decode($candidate->signals_json, true);
                    $age = $candidate->purchase_date ? \Carbon\Carbon::parse($candidate->purchase_date)->diffInYears(now()) : 'N/A';
                    @endphp
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-2">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 group-hover:text-cyan-500 transition-colors">
                                    <i class="w-5 h-5">🚌</i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $candidate->name }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $candidate->vehicle_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <p class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $age }} Years</p>
                            <p class="text-[10px] text-slate-500 italic">Aging Asset</p>
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-black {{ $candidate->replacement_score >= 80 ? 'text-rose-500' : ($candidate->replacement_score >= 60 ? 'text-amber-500' : 'text-emerald-500') }}">
                                    {{ $candidate->replacement_score }}
                                </span>
                                <div class="h-1 w-12 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full {{ $candidate->replacement_score >= 80 ? 'bg-rose-500' : ($candidate->replacement_score >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $candidate->replacement_score }}%;"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter
                                    {{ $candidate->recommendation === 'replace' ? 'bg-rose-500/10 text-rose-500 animate-pulse' : 
                                       ($candidate->recommendation === 'monitor' ? 'bg-amber-500/10 text-amber-500' : 'bg-emerald-500/10 text-emerald-500') }}">
                                {{ $candidate->recommendation }}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-right">
                            <a href="{{ url('/portal/vehicles/' . $candidate->vehicle_id) }}" class="text-[10px] font-black text-cyan-500 uppercase hover:underline">Analysis &rarr;</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-sm text-slate-500">Fleet intelligence currently processing. Results update weekly.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-100 dark:border-slate-800">
            <div class="flex items-center space-x-2 mb-4">
                <span class="text-lg">💰</span>
                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Capital Planning Insight</h4>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Expected Margin Increase</p>
                    <p class="text-2xl font-black text-emerald-500">+{{ $data['fleet_replacement']['margin_increase_pct'] }}%</p>
                    <p class="text-[10px] text-slate-500 mt-1">Via modernization efficiency</p>
                </div>
                <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Projected OpEx Savings</p>
                    <p class="text-2xl font-black text-cyan-500">{{ number_format($data['fleet_replacement']['projected_savings']) }} AED</p>
                    <p class="text-[10px] text-slate-500 mt-1">Annual maintenance reduction</p>
                </div>
            </div>
            <div class="mt-6">
                <button class="w-full py-2.5 bg-slate-900 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest rounded-lg hover:opacity-90 transition-opacity">
                    Download CapEx Report
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Phase 7L: AI Dynamic Pricing Intelligence --}}
<div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-sm p-6 mb-6 overflow-hidden relative">
    <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/5 blur-3xl -mr-32 -mt-32 pointer-events-none"></div>

    <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
                <span class="p-1 px-2 rounded bg-cyan-500/10 text-cyan-400 text-[8px] font-black uppercase tracking-widest border border-cyan-500/20">AI Dynamic Pricing</span>
                <div class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></div>
            </div>
            <h3 class="text-lg font-black text-white uppercase tracking-tight">💰 Revenue Optimization Panel</h3>
            <p class="text-[11px] text-slate-400 font-medium mt-1">Real-time yield management based on utilization, seasonality, and urgency signals</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-12">
            <div class="space-y-1">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Revenue Lift</p>
                <div class="flex items-baseline gap-1.5">
                    <p class="text-2xl font-black text-emerald-400 leading-none">+{{ $data['pricing_optimization']['revenue_lift_pct'] }}%</p>
                    <svg class="w-3 h-3 text-emerald-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="space-y-1">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">AI Generated</p>
                <p class="text-2xl font-black text-white leading-none">AED {{ number_format($data['pricing_optimization']['total_ai_revenue'], 0) }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Optimized Avg</p>
                <p class="text-2xl font-black text-cyan-400 leading-none">AED {{ number_format($data['pricing_optimization']['avg_optimized_rate'], 0) }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Market Volume</p>
                <p class="text-2xl font-black text-white/40 leading-none">{{ $data['pricing_optimization']['decisions_count'] }} <span class="text-[10px]">Qs</span></p>
            </div>
        </div>
    </div>

    @if($data['pricing_optimization']['has_data'])
    <div class="mt-8 pt-6 border-t border-slate-800">
        <h4 class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Recent Automated Decisions</h4>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @foreach($data['pricing_optimization']['recent_decisions'] as $decision)
            <div class="bg-white/5 border border-white/5 p-3 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[9px] font-black text-white/40 uppercase tracking-widest">Opt. Rate</span>
                    <span class="text-[10px] font-black text-emerald-400">AED {{ number_format($decision->optimized_rate, 0) }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    @php $m = is_string($decision->multipliers_json) ? json_decode($decision->multipliers_json, true) : (array)$decision->multipliers_json; @endphp
                    @if(($m['utilization_multiplier'] ?? 1) > 1) <span class="w-1.5 h-1.5 rounded-full bg-cyan-500" title="High Utilization"></span> @endif
                    @if(($m['season_multiplier'] ?? 1) > 1) <span class="w-1.5 h-1.5 rounded-full bg-amber-500" title="Seasonal Demand"></span> @endif
                    @if(($m['urgency_multiplier'] ?? 1) > 1) <span class="w-1.5 h-1.5 rounded-full bg-rose-500" title="Booking Urgency"></span> @endif
                    <span class="text-[9px] text-slate-500 font-bold uppercase truncate">Veh #{{ $decision->vehicle_id }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>


{{-- ═══════════════════════════════════════════════
             3️⃣ COMPANY HEALTH & 4️⃣ BRANCH INSIGHTS
        ═══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Company Health Gauge (Hero - Spans 2 cols) --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-8 shadow-sm relative overflow-hidden flex flex-col items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-{{ $data['risk_score']['color'] ?? 'emerald' }}-50/20 dark:to-{{ $data['risk_score']['color'] ?? 'emerald' }}-900/10 pointer-events-none"></div>

        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 relative z-10 w-full text-center">Company Health Score</h3>

        {{-- Massive Circular Gauge --}}
        <div class="relative w-48 h-48 mb-8 z-10">
            <svg class="w-full h-full transform -rotate-90">
                <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="12" fill="transparent"
                    stroke-dasharray="502"
                    stroke-dashoffset="{{ 502 - (502 * ($data['risk_score']['score'] ?? 100) / 100) }}"
                    class="{{ ($data['risk_score']['color'] ?? 'emerald') === 'emerald' ? 'text-emerald-500' : (($data['risk_score']['color'] ?? 'emerald') === 'amber' ? 'text-amber-500' : 'text-rose-500') }} transition-all duration-1500 ease-out" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-5xl font-black text-slate-900 dark:text-white leading-none tracking-tighter">{{ $data['risk_score']['score'] ?? 100 }}</span>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">/ 100</span>
            </div>
        </div>

        <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-8 z-10 shadow-sm border
                    {{ ($data['risk_score']['color'] ?? 'emerald') === 'emerald' ? 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 
                       (($data['risk_score']['color'] ?? 'emerald') === 'amber' ? 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' : 
                       'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20 animate-pulse') }}">
            {{ $data['risk_score']['label'] ?? 'Healthy' }}
        </span>

        {{-- Horizontal Mini Metrics --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full z-10 border-t border-gray-100 dark:border-slate-800 pt-6">
            @php
            // Map factors to our 4 requested metrics, falling back to 0 if not present
            $factors = collect($data['risk_score']['factors'] ?? []);

            $maintOverdue = $factors->firstWhere('label', 'Maintenance Overdue');
            $driverDocs = $factors->firstWhere('label', 'Missing/Expired Driver Documents');
            $arAging = $factors->firstWhere('label', 'Unpaid Invoices (>30 Days)');
            $fines = collect($data['fine_recovery'] ?? [])->sum('unpaid_amount') > 0 ? 1 : 0; // Simplified fine check
            @endphp

            <div class="flex flex-col items-center text-center">
                <div class="flex items-center space-x-1.5 mb-1">
                    @if(isset($maintOverdue) && $maintOverdue['penalty'] > 0)
                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm font-black text-rose-500">{{ $maintOverdue['penalty'] }}%</span>
                    @else
                    <svg class="w-4 h-4 text-emerald-500" bg-emerald-100 rounded-full fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm font-black text-slate-900 dark:text-white">0%</span>
                    @endif
                </div>
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Maint. Overdue</span>
            </div>

            <div class="flex flex-col items-center text-center border-l border-gray-100 dark:border-slate-800">
                <div class="flex items-center space-x-1.5 mb-1">
                    @if(isset($driverDocs) && $driverDocs['penalty'] > 0)
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm font-black text-amber-500">{{ $driverDocs['penalty'] }}%</span>
                    @else
                    <svg class="w-4 h-4 text-emerald-500" bg-emerald-100 rounded-full fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm font-black text-slate-900 dark:text-white">0%</span>
                    @endif
                </div>
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Driver Docs</span>
            </div>

            <div class="flex flex-col items-center text-center border-l border-gray-100 dark:border-slate-800">
                <div class="flex items-center space-x-1.5 mb-1">
                    @if(isset($arAging) && $arAging['penalty'] > 0)
                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm font-black text-rose-500">{{ $arAging['penalty'] }}%</span>
                    @else
                    <svg class="w-4 h-4 text-emerald-500" bg-emerald-100 rounded-full fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm font-black text-slate-900 dark:text-white">0%</span>
                    @endif
                </div>
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">AR Aging</span>
            </div>

            <div class="flex flex-col items-center text-center border-l border-gray-100 dark:border-slate-800">
                <div class="flex items-center space-x-1.5 mb-1">
                    @if($fines > 0)
                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm font-black text-rose-500">Alert</span>
                    @else
                    <svg class="w-4 h-4 text-emerald-500" bg-emerald-100 rounded-full fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm font-black text-slate-900 dark:text-white">0%</span>
                    @endif
                </div>
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Fines Overdue</span>
            </div>
        </div>
    </div>

    {{-- Branch Insights (Right Col) --}}
    @if($data['branch_benchmarks']['has_data'] ?? false)
    <div class="flex flex-col gap-4">
        {{-- Insight Badge 1: Best Branch --}}
        <div class="bg-white dark:bg-slate-900 border border-emerald-100 dark:border-emerald-900/30 rounded-2xl p-6 shadow-sm flex items-center shadow-[0_4px_20px_-4px_theme('colors.emerald.50')] dark:shadow-none transition-transform hover:scale-105">
            <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center shrink-0 mr-4">
                <span class="text-xl">🏆</span>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Best Performing Branch</p>
                <p class="text-lg font-black text-slate-900 dark:text-white truncate">{{ $data['branch_benchmarks']['best_branch']['name'] ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Insight Badge 2: Highest Risk --}}
        <div class="bg-white dark:bg-slate-900 border border-rose-100 dark:border-rose-900/30 rounded-2xl p-6 shadow-sm flex items-center shadow-[0_4px_20px_-4px_theme('colors.rose.50')] dark:shadow-none transition-transform hover:scale-105">
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center shrink-0 mr-4">
                <span class="text-xl text-rose-500">⚠</span>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Highest Risk Exposure</p>
                <p class="text-lg font-black text-slate-900 dark:text-white truncate">{{ $data['branch_benchmarks']['worst_branch']['name'] ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Insight Badge 3: Highest Margin --}}
        <div class="bg-white dark:bg-slate-900 border border-cyan-100 dark:border-cyan-900/30 rounded-2xl p-6 shadow-sm flex items-center shadow-[0_4px_20px_-4px_theme('colors.cyan.50')] dark:shadow-none transition-transform hover:scale-105 h-full">
            <div class="w-12 h-12 rounded-full bg-cyan-50 dark:bg-cyan-500/10 flex items-center justify-center shrink-0 mr-4">
                <span class="text-xl">💰</span>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Highest Margin</p>
                <p class="text-lg font-black text-slate-900 dark:text-white truncate">{{ $data['branch_benchmarks']['highest_margin']['name'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- ═══════════════════════════════════════════════
             5️⃣ FLEET SNAPSHOT & UTILIZATION TREND
        ═══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Left: Fleet Snapshot KPIs (col-span-1) --}}
    <div class="space-y-4">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800 pb-2">Fleet Snapshot</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Utilization</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white">{{ number_format($data['efficiency']['idle_pct'] ?? 0, 0) == 0 ? 100 : 100 - number_format($data['efficiency']['idle_pct'], 0) }}%</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Rev / KM</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white">{{ isset($data['efficiency']['revenue_per_km']) ? number_format($data['efficiency']['revenue_per_km'], 1) : '0' }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Maint / KM</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white">{{ isset($data['efficiency']['maintenance_cost_per_km']) ? number_format($data['efficiency']['maintenance_cost_per_km'], 1) : '0' }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Idle Units</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white">{{ $data['efficiency']['idle_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Utilization Trend Chart (col-span-2) --}}
    <div class="lg:col-span-2">
        <div class="flex items-center justify-between mb-3 border-b border-gray-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Fleet Utilization Trend</h3>
            <span class="text-[10px] font-black text-cyan-500 uppercase tracking-widest">Last 30 Days</span>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm h-[200px]" data-chart-container="utilization">
            {{-- Chart injected via JS --}}
        </div>
    </div>
</div>

@include('company.dashboard.partials.quick-actions')



<!-- {{-- Quick Actions FAB --}}
        <x-dashboard.quick-actions /> -->

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Revenue Trend Chart ──────────────────────────────────────────
        const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
        const trendGradient = trendCtx.createLinearGradient(0, 0, 0, 300);
        trendGradient.addColorStop(0, 'rgba(6, 182, 212, 0.35)');
        trendGradient.addColorStop(1, 'rgba(6, 182, 212, 0.0)');

        const expenseGradient = trendCtx.createLinearGradient(0, 0, 0, 300);
        expenseGradient.addColorStop(0, 'rgba(239, 68, 68, 0.25)');
        expenseGradient.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        const revenueData = {
            monthly: <?= json_encode($data['charts']['revenue_trend_monthly']) ?>,
            daily: <?= json_encode($data['charts']['revenue_trend_daily']) ?>
        };

        const revenueChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: revenueData.monthly.labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData.monthly.revenue,
                    borderColor: '#06b6d4',
                    borderWidth: 3,
                    backgroundColor: trendGradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0f172a',
                    pointBorderColor: '#06b6d4',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: 'Expenses',
                    data: revenueData.monthly.expenses,
                    borderColor: '#ef4444',
                    borderWidth: 2,
                    backgroundColor: expenseGradient,
                    fill: true,
                    tension: 0.4,
                    borderDash: [5, 3],
                    pointBackgroundColor: '#0f172a',
                    pointBorderColor: '#ef4444',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 2,
                            usePointStyle: true,
                            pointStyle: 'line',
                            font: {
                                size: 10,
                                weight: 'bold'
                            },
                            color: '#64748b'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': AED ' + ctx.parsed.y.toLocaleString(undefined, {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            })
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            color: '#64748b',
                            callback: v => 'AED ' + v.toLocaleString()
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            color: '#64748b'
                        }
                    }
                }
            }
        });

        window.switchRevenueView = function(view) {
            const data = revenueData[view];
            revenueChart.data.labels = data.labels;
            revenueChart.data.datasets[0].data = data.revenue;
            revenueChart.data.datasets[1].data = data.expenses;
            revenueChart.update();
        };

        // ── Utilization Trend Chart ───────────────────────────────────────
        const utilCtx = document.createElement('canvas');
        utilCtx.id = 'utilizationTrendChart';
        const utilContainer = document.querySelector('[data-chart-container="utilization"]');
        if (utilContainer) {
            utilContainer.innerHTML = '';
            utilContainer.appendChild(utilCtx);
            
            const utilGradient = utilCtx.getContext('2d').createLinearGradient(0, 0, 0, 300);
            utilGradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            utilGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(utilCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($data['charts']['utilization_trend']['labels']) ?>,
                    datasets: [{
                        label: 'Active Vehicles',
                        data: <?= json_encode($data['charts']['utilization_trend']['values']) ?>,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        backgroundColor: utilGradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 0,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: { color: '#64748b', font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { size: 10 } }
                        }
                    }
                }
            });
        }

        // ── Branch Performance Chart (Bar) ──────────────────────────────────
        <?php if ($data['branch_benchmarks']['has_data']): ?>
            const perfCtx = document.getElementById('branchPerformanceChart').getContext('2d');
            new Chart(perfCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($data['branch_benchmarks']['chart_data']['labels']) ?>,
                    datasets: [{
                            label: 'Revenue',
                            data: <?= json_encode($data['branch_benchmarks']['chart_data']['revenue']) ?>,
                            backgroundColor: 'rgba(6, 182, 212, 0.7)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Margin',
                            data: <?= json_encode($data['branch_benchmarks']['chart_data']['margin']) ?>,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Utilization',
                            data: <?= json_encode($data['branch_benchmarks']['chart_data']['utilization']) ?>,
                            backgroundColor: 'rgba(245, 158, 11, 0.7)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 10,
                                font: {
                                    size: 10
                                },
                                color: '#64748b'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#64748b'
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#64748b'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // ── Branch Growth Trend (Line) ──────────────────────────────────────
            const growthCtx = document.getElementById('branchGrowthChart').getContext('2d');
            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($data['branch_benchmarks']['growth_trends']['labels']) ?>,
                    datasets: [
                        <?php foreach ($data['branch_benchmarks']['growth_trends']['series'] as $index => $series): ?> {
                                label: '<?= $series["name"] ?>',
                                data: <?= json_encode($series["data"]) ?>,
                                borderColor: ['#06b6d4', '#10b981', '#f59e0b', '#6366f1', '#ec4899'][<?= $index ?> % 5],
                                borderWidth: 2,
                                tension: 0.4,
                                fill: false,
                                pointRadius: 3
                            },
                        <?php endforeach; ?>
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 10,
                                font: {
                                    size: 10
                                },
                                color: '#64748b'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#64748b',
                                callback: v => 'AED ' + v.toLocaleString()
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#64748b'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        <?php endif; ?>

        // ── Predictive Risk Trend Chart (Phase 7N) ──────────────────────────
        const riskTrendCtx = document.getElementById('riskTrendChart').getContext('2d');
        new Chart(riskTrendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($data['predictive_risk']['trends']['labels']) ?>,
                datasets: [{
                        label: 'Breakdown Risk',
                        data: <?= json_encode($data['predictive_risk']['trends']['vehicle_data']) ?>,
                        borderColor: '#f43f5e', // rose-500
                        backgroundColor: 'rgba(244, 63, 94, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#f43f5e'
                    },
                    {
                        label: 'Accident Risk',
                        data: <?= json_encode($data['predictive_risk']['trends']['driver_data']) ?>,
                        borderColor: '#f59e0b', // amber-500
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#f59e0b'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 11
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y}%`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 10
                            },
                            callback: v => v + '%'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
        // ── View Toggle Logic ──────────────────────────────────────────────
        const btnExec = document.getElementById('btn-exec-view');
        const btnOps = document.getElementById('btn-ops-view');
        const opsDetails = document.querySelectorAll('.ops-detail');
        const mapContainer = document.getElementById('zone3-map-container');

        function setView(view) {
            if (view === 'executive') {
                if (btnExec) {
                    btnExec.className = "px-4 py-1.5 text-xs font-black uppercase tracking-widest rounded-md bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm transition-all border border-slate-200 dark:border-slate-800";
                }
                if (btnOps) {
                    btnOps.className = "px-4 py-1.5 text-xs font-black uppercase tracking-widest rounded-md text-slate-500 hover:text-slate-900 dark:text-white transition-all border border-transparent";
                }
                opsDetails.forEach(el => {
                    el.classList.add('opacity-0');
                    setTimeout(() => el.classList.add('hidden'), 300);
                });
                if (mapContainer) {
                    mapContainer.classList.remove('lg:col-span-2');
                    mapContainer.classList.add('lg:col-span-3');
                }
            } else {
                if (btnOps) {
                    btnOps.className = "px-4 py-1.5 text-xs font-black uppercase tracking-widest rounded-md bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm transition-all border border-slate-200 dark:border-slate-800";
                }
                if (btnExec) {
                    btnExec.className = "px-4 py-1.5 text-xs font-black uppercase tracking-widest rounded-md text-slate-500 hover:text-slate-900 dark:text-white transition-all border border-transparent";
                }
                opsDetails.forEach(el => {
                    el.classList.remove('hidden');
                    setTimeout(() => el.classList.remove('opacity-0'), 10);
                });
                if (mapContainer) {
                    mapContainer.classList.remove('lg:col-span-3');
                    mapContainer.classList.add('lg:col-span-2');
                }
            }
        }

        if (btnExec) btnExec.addEventListener('click', () => setView('executive'));
        if (btnOps) btnOps.addEventListener('click', () => setView('operations'));

        // Set initial state
        setView('executive');

    });
</script>
@endpush