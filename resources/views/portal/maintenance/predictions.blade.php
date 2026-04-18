@extends('layouts.company')

@section('title', 'Predictive Maintenance')
@section('header_title', 'Operational Intelligence')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight uppercase italic text-rose-500">Predictive Maintenance</h2>
            <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-bold">Risk-based fleet health monitoring</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('company.maintenance.predictions.run') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black uppercase tracking-widest rounded-xl transition shadow-lg shadow-blue-900/40 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Run Full Analysis
                </button>
            </form>
        </div>
    </div>

    {{-- Risk Summary Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-rose-500/10 border border-rose-500/20 p-6 rounded-3xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full"></div>
            <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest">High Risk Vehicles</p>
            <p class="text-4xl font-black text-white mt-1">{{ $predictions->where('risk_level', 'high')->count() }}</p>
            <p class="text-xs text-rose-500/70 mt-2 font-bold">Immediate attention required</p>
        </div>
        <div class="bg-amber-500/10 border border-amber-500/20 p-6 rounded-3xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full"></div>
            <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest">Medium Risk</p>
            <p class="text-4xl font-black text-white mt-1">{{ $predictions->where('risk_level', 'medium')->count() }}</p>
            <p class="text-xs text-amber-500/70 mt-2 font-bold">Planned service required</p>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-3xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full"></div>
            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Low Risk / Healthy</p>
            <p class="text-4xl font-black text-white mt-1">{{ $predictions->where('risk_level', 'low')->count() }}</p>
            <p class="text-xs text-emerald-500/70 mt-2 font-bold">Optimal operating status</p>
        </div>
    </div>

    {{-- Predictions Table --}}
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
        <div class="px-8 py-5 border-b border-slate-800 bg-slate-800/20 flex items-center justify-between">
            <h3 class="text-sm font-black text-white uppercase tracking-widest">Maintenance Forecast</h3>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Alpha ML v2.4</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/30 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-5">Vehicle Details</th>
                        <th class="px-8 py-5">Risk Status</th>
                        <th class="px-8 py-5 text-center">Cost Trend</th>
                        <th class="px-8 py-5">Est. Service Date</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @forelse($predictions as $prediction)
                    <tr class="hover:bg-slate-800/20 transition group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-blue-400 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1m-4-1a1 1 0 001 1h1m-5 1v1h11v-1"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-white">{{ $prediction->vehicle->name }}</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tight">{{ $prediction->vehicle->plate_number }} · {{ number_format($prediction->vehicle->current_odometer) }} km</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $colors = match($prediction->risk_level) {
                                    'high' => ['bg-rose-500/15', 'text-rose-400', 'border-rose-500/30'],
                                    'medium' => ['bg-amber-500/15', 'text-amber-400', 'border-amber-500/30'],
                                    default => ['bg-emerald-500/15', 'text-emerald-400', 'border-emerald-500/30'],
                                };
                            @endphp
                            <div class="flex flex-col gap-1">
                                <span class="w-fit px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $colors[0] }} {{ $colors[1] }} border {{ $colors[2] }}">
                                    {{ $prediction->risk_level }}
                                </span>
                                <p class="text-[10px] text-slate-600 font-bold ml-1">Daily Avg: {{ $prediction->avg_km_per_day }} km</p>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($prediction->cost_growth_percentage > 0)
                                <div class="flex flex-col items-center">
                                    <span class="text-rose-400 font-black text-xs flex items-center gap-1">
                                        +{{ number_format($prediction->cost_growth_percentage, 1) }}%
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                    </span>
                                    <p class="text-[9px] text-slate-600 font-black uppercase tracking-widest mt-0.5">Escalation</p>
                                </div>
                            @elseif($prediction->cost_growth_percentage < 0)
                                <div class="flex flex-col items-center">
                                    <span class="text-emerald-400 font-black text-xs flex items-center gap-1">
                                        {{ number_format($prediction->cost_growth_percentage, 1) }}%
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </span>
                                    <p class="text-[9px] text-slate-600 font-black uppercase tracking-widest mt-0.5">Stabilized</p>
                                </div>
                            @else
                                <span class="text-slate-500 font-black text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-white">{{ Carbon\Carbon::parse($prediction->predicted_service_date)->format('d M Y') }}</p>
                            <p class="text-[10px] text-slate-500 font-medium">In ≈ {{ now()->diffInDays($prediction->predicted_service_date) }} days</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('company.maintenance.create', ['vehicle_id' => $prediction->vehicle_id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-[10px] font-black uppercase tracking-widest rounded-lg transition border border-slate-700">
                                Schedule
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <p class="text-slate-500 font-black uppercase tracking-widest text-sm">No analysis data available</p>
                            <p class="text-xs text-slate-600 mt-2">Click "Run Full Analysis" to generate the latest predictions.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Strategy Card --}}
    <div class="bg-blue-600/5 border border-blue-500/10 rounded-3xl p-8 flex items-center gap-8">
        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-white shrink-0 shadow-lg shadow-blue-500/30">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h4 class="text-white font-black uppercase tracking-tight italic">How it works</h4>
            <p class="text-slate-500 text-sm mt-1 max-w-2xl">Our predictive engine analyzes historical odometer logs, recent maintenance cost growth trends, and usage stress factors. High-risk rankings indicate vehicles where failure probability is high based on recent fuel consumption drops and increased repair frequency.</p>
        </div>
    </div>

</div>
@endsection
