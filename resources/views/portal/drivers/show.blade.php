@extends('layouts.company')

@section('title', $driver->name . ' — Driver Profile')

@section('header_title')
<div class="flex items-center gap-3">
    <a href="{{ route('company.drivers.index') }}" class="flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>
    <div>
        <h1 class="text-base font-black text-slate-900 dark:text-white tracking-tight uppercase leading-none">Driver Profile</h1>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Personnel Dossier</p>
    </div>
</div>
@endsection

@section('content')
@php
$daysTilExpiry = now()->diffInDays($driver->license_expiry_date, false);
$isExpired = $daysTilExpiry < 0;
    $isWarning=!$isExpired && $daysTilExpiry < 30;
    $isActive=$driver->status === 'active';
    @endphp

    <div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500">

        {{-- ===== HERO HEADER ===== --}}
        <div class="relative bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            {{-- Gradient bar --}}
            <div class="h-1.5 w-full bg-gradient-to-r from-blue-500 via-cyan-400 to-emerald-400"></div>

            <div class="p-6 flex flex-col md:flex-row md:items-center gap-6">
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-blue-500/25">
                        {{ strtoupper(substr($driver->name, 0, 1)) }}
                    </div>
                    <div class="absolute -bottom-1.5 -right-1.5 w-5 h-5 rounded-full border-2 border-white dark:border-slate-900 {{ $isActive ? 'bg-emerald-500' : 'bg-slate-400' }}"></div>
                </div>

                {{-- Identity --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $driver->name }}</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                        {{ $isActive ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ ucfirst($driver->status) }}
                        </span>
                        @if($isExpired)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 text-[9px] font-black uppercase tracking-widest">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                            </svg>
                            License Expired
                        </span>
                        @elseif($isWarning)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 text-[9px] font-black uppercase tracking-widest">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                            </svg>
                            Expires in {{ $daysTilExpiry }}d
                        </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-4 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2" />
                            </svg>
                            ID: {{ str_pad($driver->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            {{ $driver->driver_code }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $driver->branch->name ?? 'Central Command' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Joined {{ $driver->hire_date->format('d M Y') }}
                        </span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('company.drivers.edit', $driver) }}"
                        class="flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('company.drivers.toggle-status', $driver) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all hover:scale-105
                        {{ $isActive ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 hover:bg-emerald-100' }}">
                            @if($isActive)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Suspend
                            @else
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Activate
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== KPI STATS ROW ===== --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Trips</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $metrics['total_trips'] ?? 0 }}</p>
                <p class="text-[9px] text-slate-400 font-medium mt-1">All time deployments</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">On-Time Rate</p>
                <p class="text-2xl font-black text-emerald-500">{{ $metrics['on_time_rate'] ?? 100 }}%</p>
                <p class="text-[9px] text-slate-400 font-medium mt-1">Punctuality score</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Completion Rate</p>
                <p class="text-2xl font-black text-blue-500">{{ $metrics['completion_rate'] ?? 0 }}%</p>
                <p class="text-[9px] text-slate-400 font-medium mt-1">Finished missions</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Revenue Generated</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($driver->rentals()->sum('final_amount'), 0) }}</p>
                <p class="text-[9px] text-slate-400 font-medium mt-1">AED total</p>
            </div>
        </div>

        {{-- ===== MAIN CONTENT GRID ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT SIDEBAR --}}
            <div class="space-y-5">

                {{-- Personal Info --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="w-7 h-7 rounded-lg bg-blue-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">Personal Info</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        @if($driver->phone)
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Phone</span>
                            <span class="text-[10px] font-bold text-slate-900 dark:text-white">{{ $driver->phone }}</span>
                        </div>
                        @endif
                        @if($driver->email)
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Email</span>
                            <span class="text-[10px] font-bold text-slate-900 dark:text-white truncate ml-2">{{ $driver->email }}</span>
                        </div>
                        @endif
                        @if($driver->national_id)
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">National ID</span>
                            <span class="text-[10px] font-bold text-slate-900 dark:text-white font-mono">{{ $driver->national_id }}</span>
                        </div>
                        @endif
                        @if($driver->address)
                        <div class="pt-1 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block mb-1">Address</span>
                            <span class="text-[10px] font-bold text-slate-900 dark:text-white leading-relaxed">{{ $driver->address }}{{ $driver->city ? ', '.$driver->city : '' }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- License & Compliance --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="w-7 h-7 rounded-lg bg-amber-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <h3 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">License & Compliance</h3>
                    </div>

                    {{-- License Expiry Gauge --}}
                    <div class="px-5 pt-5 pb-3">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0
                            {{ $isExpired ? 'bg-rose-50 dark:bg-rose-500/10' : ($isWarning ? 'bg-amber-50 dark:bg-amber-500/10' : 'bg-emerald-50 dark:bg-emerald-500/10') }}">
                                @if($isExpired)
                                <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @elseif($isWarning)
                                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                @else
                                <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">License Status</p>
                                <p class="text-sm font-black {{ $isExpired ? 'text-rose-500' : ($isWarning ? 'text-amber-500' : 'text-emerald-500') }} uppercase">
                                    {{ $isExpired ? 'Expired' : ($isWarning ? 'Expiring Soon' : 'Valid') }}
                                </p>
                                @if($isExpired)
                                <p class="text-[9px] text-rose-400 font-bold">Expired {{ abs($daysTilExpiry) }} days ago</p>
                                @elseif($isWarning)
                                <p class="text-[9px] text-amber-400 font-bold">{{ $daysTilExpiry }} days remaining</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="px-5 pb-5 space-y-3">
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">License #</span>
                                <span class="text-[10px] font-black text-slate-900 dark:text-white font-mono">{{ $driver->license_number }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Class</span>
                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[9px] font-black uppercase rounded-md">{{ $driver->license_type }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Expiry</span>
                                <span class="text-[10px] font-bold {{ $isExpired ? 'text-rose-500' : ($isWarning ? 'text-amber-500' : 'text-slate-900 dark:text-white') }}">
                                    {{ $driver->license_expiry_date->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Employment --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">Employment</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Hire Date</span>
                            <span class="text-[10px] font-bold text-slate-900 dark:text-white">{{ $driver->hire_date->format('d M Y') }}</span>
                        </div>
                        @if($driver->salary)
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Base Salary</span>
                            <span class="text-[10px] font-bold text-slate-900 dark:text-white">{{ number_format($driver->salary, 2) }} AED</span>
                        </div>
                        @endif
                        @if($driver->commission_rate)
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Commission</span>
                            <span class="text-[10px] font-bold text-emerald-500">{{ $driver->commission_rate }}%</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Tenure</span>
                            <span class="text-[10px] font-bold text-slate-900 dark:text-white">{{ $driver->hire_date->diffForHumans(null, true) }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT: DEPLOYMENT HISTORY --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Emergency Contact --}}
                @if($driver->emergency_contact_name)
                <div class="flex items-center gap-4 p-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Emergency Contact</p>
                        <p class="text-xs font-black text-slate-900 dark:text-white">{{ $driver->emergency_contact_name }}</p>
                    </div>
                    @if($driver->emergency_contact_phone)
                    <div class="ml-auto">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Phone</p>
                        <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $driver->emergency_contact_phone }}</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Notes --}}
                @if($driver->notes)
                <div class="p-4 bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-2xl">
                    <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-1.5">Operational Notes</p>
                    <p class="text-[11px] text-blue-700 dark:text-blue-300 font-medium leading-relaxed">{{ $driver->notes }}</p>
                </div>
                @endif

                {{-- Deployment History --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-violet-500/10 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h2 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">Rental History</h2>
                        </div>
                        <span class="text-[9px] font-black text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg uppercase">
                            {{ $recentrentals->count() }} record{{ $recentrentals->count() !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800">
                                    <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Rental</th>
                                    <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                                    <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Period</th>
                                    <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Amount</th>
                                    <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($recentrentals as $rental)
                                @php
                                $sc = ['draft'=>'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400','confirmed'=>'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400','active'=>'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400','completed'=>'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400','cancelled'=>'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400'];
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('company.rentals.show', $rental) }}" class="text-[10px] font-black text-blue-500 hover:text-blue-600 hover:underline uppercase tracking-tight">
                                            {{ $rental->rental_number }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 text-[10px] font-bold text-slate-900 dark:text-white">{{ $rental->customer->name ?? '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="text-[9px] font-bold text-slate-700 dark:text-slate-300">{{ $rental->start_date->format('d M Y') }}</div>
                                        <div class="text-[9px] text-slate-400 font-medium">→ {{ $rental->end_date->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-[10px] font-black text-slate-900 dark:text-white">
                                        {{ number_format($rental->final_amount, 2) }}
                                        <span class="text-[8px] text-slate-400 font-medium">AED</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $sc[$rental->status] ?? 'bg-slate-100 text-slate-500' }}">
                                            {{ $rental->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">No Deployment History</p>
                                            <p class="text-[10px] text-slate-400">This driver has not been assigned to any rentals yet.</p>
                                            <a href="{{ route('company.rentals.create') }}" class="mt-1 px-4 py-2 bg-slate-900 dark:bg-white dark:text-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:scale-105 transition-transform">
                                                Create Rental
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endsection