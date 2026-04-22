@extends('layouts.company')

@section('title', 'UAE/Gulf Traffic Fine Checker')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    body, .fine-checker-root { font-family: 'DM Sans', sans-serif; }
    .fine-checker-root {
        background: #0A0F1E;
        min-height: 100vh;
        color: #E2E8F0;
    }
    /* Glassmorphism cards */
    .glass-card {
        background: rgba(17, 24, 39, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid #1F2937;
        border-radius: 16px;
    }
    /* Authority card hover glow */
    .authority-card {
        transition: all 0.25s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }
    .authority-card:hover {
        transform: translateY(-4px) scale(1.03);
        border-color: #F59E0B;
        box-shadow: 0 0 20px rgba(245,158,11,0.25);
    }
    .authority-card.selected {
        border-color: #F59E0B !important;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.3) !important;
        animation: borderPulse 1.5s ease-in-out infinite;
    }
    @keyframes borderPulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(245,158,11,0.3); }
        50% { box-shadow: 0 0 0 6px rgba(245,158,11,0.1); }
    }
    /* Shimmer effect */
    .shimmer-btn {
        position: relative;
        overflow: hidden;
    }
    .shimmer-btn::after {
        content:'';
        position: absolute;
        top: 0; left: -100%;
        width: 60%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        animation: shimmer 2.2s infinite;
    }
    @keyframes shimmer { to { left: 200%; } }
    /* Pulse badge */
    .pulse-red {
        animation: pulseRed 1.5s ease-in-out infinite;
    }
    @keyframes pulseRed {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.5); }
        50% { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
    }
    /* Count-up anim handled in alpine */
    .stat-value { transition: color 0.3s ease; }
    /* Slide panel */
    .slide-panel {
        transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease;
    }
    /* Table row hover */
    .fine-row { transition: background 0.15s ease; }
    .fine-row:hover { background: rgba(31,41,55,0.8); }
    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #0A0F1E; }
    ::-webkit-scrollbar-thumb { background: #374151; border-radius: 3px; }
    /* Toast */
    .toast-container { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; }
    /* Portal link glow */
    .portal-link-btn:hover {
        box-shadow: 0 0 12px rgba(245,158,11,0.4);
    }
</style>
@endpush

@section('header_title')
<div class="flex items-center space-x-3">
    <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
    <h1 class="text-lg font-bold text-white tracking-tight uppercase">🚦 UAE/Gulf Fine Checker</h1>
</div>
@endsection

@section('content')
@php
$authorityLinks = [
    'DXB' => 'https://www.dubaipolice.gov.ae/app/services/fine-payment/search',
    'RTA' => 'https://www.rta.ae',
    'AUH' => 'https://www.adpolice.gov.ae/en/services/traffic-services/traffic-fines-inquiry',
    'SHJ' => 'https://www.sharjahpolice.gov.ae',
    'AJM' => 'https://www.ajmanpolice.gov.ae',
    'RAK' => 'https://www.rakpolice.gov.ae',
    'FUJ' => 'https://www.fujairahpolice.gov.ae',
    'UAQ' => 'https://uaqpolice.gov.ae',
    'SAR' => 'https://www.absher.sa',
    'SAJ' => 'https://www.absher.sa',
    'SAD' => 'https://www.absher.sa',
    'KWT' => 'https://www.moi.gov.kw',
    'BAH' => 'https://www.bahrain.bh',
    'QAT' => 'https://www.moi.gov.qa',
    'OMA' => 'https://www.rop.gov.om',
];
@endphp
<div class="fine-checker-root -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 px-4 sm:px-6 lg:px-8 pt-6 pb-12"
     x-data="fineChecker()"
     x-init="initCounters()">

    {{-- ─────────────────────── TOAST ─────────────────────── --}}
    <div class="toast-container space-y-2">
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="flex items-center space-x-3 bg-emerald-900 border border-emerald-500/40 text-emerald-300 px-5 py-3.5 rounded-xl shadow-xl">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
            <button @click="show=false" class="ml-2 text-emerald-400 hover:text-white">&times;</button>
        </div>
        @endif
    </div>

    {{-- ─────────────────────── SECTION 1: HEADER + STATS ─────────────────────── --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">🚦 UAE/Gulf Traffic Fine Checker</h2>
                <p class="text-sm text-slate-400 mt-1">Check, record and manage traffic fines across all GCC authorities</p>
            </div>
            <button @click="openPanel = true"
                class="shimmer-btn inline-flex items-center space-x-2 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black text-sm px-5 py-3 rounded-xl shadow-lg shadow-amber-500/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>RECORD FINE</span>
            </button>
        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            {{-- Total Outstanding --}}
            <div class="glass-card p-4 {{ $totalOutstanding > 0 ? 'border-red-500/50' : '' }}">
                <p class="text-[10px] font-black uppercase tracking-widest {{ $totalOutstanding > 0 ? 'text-red-400' : 'text-slate-500' }} mb-1">Total Outstanding</p>
                <p class="text-2xl font-black {{ $totalOutstanding > 0 ? 'text-red-400' : 'text-slate-300' }} stat-value"
                   x-text="'AED ' + formatNum({{ (int)$totalOutstanding }})">AED 0</p>
            </div>
            {{-- Unpaid Count --}}
            <div class="glass-card p-4 {{ $unpaidCount > 0 ? 'border-red-500/50' : '' }}">
                <p class="text-[10px] font-black uppercase tracking-widest {{ $unpaidCount > 0 ? 'text-red-400' : 'text-slate-500' }} mb-1">Unpaid Fines</p>
                <p class="text-2xl font-black {{ $unpaidCount > 0 ? 'text-red-400' : 'text-slate-300' }} stat-value"
                   x-text="countUp({{ $unpaidCount }}) + ' Violations'">0</p>
            </div>
            {{-- Paid This Month --}}
            <div class="glass-card p-4 border-emerald-500/20">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400 mb-1">Paid This Month</p>
                <p class="text-2xl font-black text-emerald-400 stat-value"
                   x-text="'AED ' + formatNum({{ (int)$paidThisMonth }})">AED 0</p>
            </div>
            {{-- Vehicles Checked --}}
            <div class="glass-card p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-sky-400 mb-1">Vehicles w/ Fines</p>
                <p class="text-2xl font-black text-sky-400 stat-value"
                   x-text="countUp({{ $vehiclesChecked }})">0</p>
            </div>
            {{-- Last Sync --}}
            <div class="glass-card p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Last Updated</p>
                <p class="text-sm font-bold text-slate-400">
                    {{ $lastSync ? \Carbon\Carbon::parse($lastSync)->diffForHumans() : 'Never' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ─────────────────────── SECTION 2: FINE CHECKER TOOL ─────────────────────── --}}
    <div class="glass-card p-6 mb-8">
        <h3 class="text-xs font-black uppercase tracking-widest text-amber-400 border-l-2 border-amber-400 pl-3 mb-6">🔍 Check By Authority</h3>

        {{-- Authority Grid --}}
        <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-6 gap-3 mb-6">
            @php
            $authorities = [
                ['code'=>'DXB','name'=>'Dubai Police','flag'=>'🇦🇪','color'=>'from-red-900/40 to-red-800/20'],
                ['code'=>'RTA','name'=>'Dubai RTA','flag'=>'🇦🇪','color'=>'from-blue-900/40 to-blue-800/20'],
                ['code'=>'AUH','name'=>'Abu Dhabi Police','flag'=>'🇦🇪','color'=>'from-emerald-900/40 to-emerald-800/20'],
                ['code'=>'SHJ','name'=>'Sharjah Police','flag'=>'🇦🇪','color'=>'from-blue-900/40 to-slate-800/20'],
                ['code'=>'AJM','name'=>'Ajman Police','flag'=>'🇦🇪','color'=>'from-slate-800/60 to-slate-700/30'],
                ['code'=>'RAK','name'=>'RAK Police','flag'=>'🇦🇪','color'=>'from-slate-800/60 to-slate-700/30'],
                ['code'=>'FUJ','name'=>'Fujairah Police','flag'=>'🇦🇪','color'=>'from-slate-800/60 to-slate-700/30'],
                ['code'=>'UAQ','name'=>'UAQ Police','flag'=>'🇦🇪','color'=>'from-slate-800/60 to-slate-700/30'],
                ['code'=>'SAR','name'=>'Saudi Muroor','flag'=>'🇸🇦','color'=>'from-green-900/40 to-green-800/20'],
                ['code'=>'KWT','name'=>'Kuwait MOI','flag'=>'🇰🇼','color'=>'from-green-900/40 to-emerald-800/20'],
                ['code'=>'BAH','name'=>'Bahrain MOI','flag'=>'🇧🇭','color'=>'from-red-900/40 to-slate-800/20'],
                ['code'=>'QAT','name'=>'Qatar MOI','flag'=>'🇶🇦','color'=>'from-purple-900/40 to-slate-800/20'],
                ['code'=>'OMA','name'=>'Oman ROP','flag'=>'🇴🇲','color'=>'from-red-900/40 to-green-900/20'],
            ];
            @endphp
            @foreach($authorities as $auth)
            <div class="authority-card glass-card p-3 text-center bg-gradient-to-br {{ $auth['color'] }}"
                 :class="{ 'selected': selectedAuthority === '{{ $auth['code'] }}' }"
                 @click="selectAuthority('{{ $auth['code'] }}', '{{ $auth['name'] }}')">
                <div class="text-2xl mb-1">{{ $auth['flag'] }}</div>
                <p class="text-[10px] font-black text-white uppercase tracking-wider leading-tight">{{ $auth['code'] }}</p>
                <p class="text-[9px] text-slate-400 font-medium mt-0.5 leading-tight">{{ Str::words($auth['name'], 2, '') }}</p>
            </div>
            @endforeach
        </div>

        {{-- Plate Input (shown when authority selected) --}}
        <div x-show="selectedAuthority" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             class="border border-amber-500/30 rounded-2xl p-5 bg-amber-500/5">

            <div class="flex items-center space-x-3 mb-4">
                <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                <p class="text-xs font-black text-amber-400 uppercase tracking-widest" x-text="'Checking: ' + selectedAuthorityName"></p>
            </div>

            <p class="text-[10px] text-slate-400 font-semibold uppercase mb-3" x-text="plateHint"></p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Plate Code --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Plate Code</label>
                    <select x-model="plateCode"
                        class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none">
                        <option value="">— Code —</option>
                        <template x-for="code in plateCodes" :key="code">
                            <option :value="code" x-text="code"></option>
                        </template>
                    </select>
                </div>
                {{-- Plate Number --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Plate Number</label>
                    <input type="text" x-model="plateNumber" placeholder="e.g. 12345"
                        class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-mono font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none placeholder-slate-600">
                </div>
                {{-- Actions --}}
                <div class="flex flex-col space-y-2 justify-end">
                    <a :href="officialLink" target="_blank" rel="noopener"
                       class="shimmer-btn flex items-center justify-center space-x-2 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black text-xs px-4 py-2.5 rounded-xl transition-all portal-link-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>CHECK OFFICIAL SITE</span>
                    </a>
                    <button @click="openPanel = true"
                        class="flex items-center justify-center space-x-2 bg-slate-700 hover:bg-slate-600 text-white font-black text-xs px-4 py-2.5 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002-2h2a2 2 0 012 2"/></svg>
                        <span>RECORD MANUALLY</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─────────────────────── SECTION 3: FLEET TABLE ─────────────────────── --}}
    <div class="glass-card p-6 mb-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xs font-black uppercase tracking-widest text-amber-400 border-l-2 border-amber-400 pl-3">🚌 Your Fleet Vehicles</h3>
            <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $vehicles->count() }} Assets Registered</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-800">
                        <th class="py-3 px-4 text-left">Vehicle</th>
                        <th class="py-3 px-4 text-left">Plate</th>
                        <th class="py-3 px-4 text-left">Emirate</th>
                        <th class="py-3 px-4 text-center">Fines</th>
                        <th class="py-3 px-4 text-center">Last Checked</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($vehicles as $vehicle)
                    @php
                        $fineCount = $vehicle->fines->count();
                        $hasPlate  = !empty($vehicle->plate_number);
                    @endphp
                    <tr class="fine-row group">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0">
                                    @if($vehicle->image_path)
                                        <img src="{{ Storage::url($vehicle->image_path) }}" alt="" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <span class="text-xs font-black text-slate-500">🚌</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-black text-white uppercase tracking-tight">{{ $vehicle->vehicle_number }}</p>
                                    <p class="text-[10px] text-slate-500 font-medium">{{ $vehicle->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            @if($hasPlate)
                            <span class="font-mono text-sm font-black text-white bg-slate-800 px-2 py-0.5 rounded border border-slate-700">
                                {{ $vehicle->plate_number }}
                            </span>
                            @else
                            <span class="text-[10px] font-bold text-slate-600 uppercase">No Plate</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">
                                {{ $vehicle->registration_emirate ?? '—' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($fineCount > 0)
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-500/20 text-red-400 text-xs font-black pulse-red border border-red-500/40">
                                {{ $fineCount }}
                            </span>
                            @else
                            <svg class="w-5 h-5 text-emerald-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="text-[10px] text-slate-500 font-medium">
                                @php
                                    try {
                                        $lastChecked = $vehicle->fines->max('last_checked_at') ?? $vehicle->fines->max('updated_at');
                                    } catch (\Exception $e) {
                                        $lastChecked = $vehicle->fines->max('updated_at');
                                    }
                                @endphp
                                {{ $lastChecked ? \Carbon\Carbon::parse($lastChecked)->diffForHumans() : 'Never' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                @if($hasPlate && $vehicle->registration_emirate)
                                <a href="{{ $authorityLinks[$vehicle->registration_emirate] ?? '#' }}" target="_blank" rel="noopener"
                                   class="shimmer-btn px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-900 text-[10px] font-black uppercase rounded-lg transition-all">
                                    CHECK NOW
                                </a>
                                @else
                                <a href="{{ route('company.fleet.edit', $vehicle) }}"
                                   class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-white text-[10px] font-black uppercase rounded-lg transition-all">
                                    ADD PLATE
                                </a>
                                @endif
                                @if($fineCount > 0)
                                <a href="#fine-records"
                                   class="px-3 py-1.5 bg-red-900/50 hover:bg-red-800/60 text-red-400 text-[10px] font-black uppercase rounded-lg border border-red-500/30 transition-all">
                                    VIEW FINES
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <p class="text-slate-500 text-sm font-semibold">No vehicles registered</p>
                            <a href="{{ route('company.fleet.create') }}" class="text-amber-400 text-xs font-black uppercase mt-2 inline-block hover:underline">Register First Vehicle →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─────────────────────── SECTION 4: AUTHORITY QUICK LINKS ─────────────────────── --}}
    <div class="mb-8">
        <h3 class="text-xs font-black uppercase tracking-widest text-amber-400 border-l-2 border-amber-400 pl-3 mb-5">🌐 Official Authority Portals</h3>
        @php
        $portalAuthorities = [
            ['flag'=>'🇦🇪','name'=>'Dubai RTA','country'=>'UAE','border'=>'border-red-500/40','services'=>['Fine Payment','Vehicle Registration','License Renewal'],'link'=>'https://www.rta.ae','bg'=>'from-red-900/20'],
            ['flag'=>'🇦🇪','name'=>'Dubai Police','country'=>'UAE','border'=>'border-blue-500/40','services'=>['Traffic Fines','Fine Payment','Inquiry'],'link'=>'https://www.dubaipolice.gov.ae/app/services/fine-payment/search','bg'=>'from-blue-900/20'],
            ['flag'=>'🇦🇪','name'=>'Abu Dhabi Police','country'=>'UAE','border'=>'border-emerald-500/40','services'=>['Traffic Fine Inquiry','Fine Payment'],'link'=>'https://www.adpolice.gov.ae/en/services/traffic-services/traffic-fines-inquiry','bg'=>'from-emerald-900/20'],
            ['flag'=>'🇦🇪','name'=>'Sharjah Police','country'=>'UAE','border'=>'border-slate-500/40','services'=>['Traffic Fines','Fine Inquiry'],'link'=>'https://www.sharjahpolice.gov.ae','bg'=>'from-slate-800/40'],
            ['flag'=>'🇦🇪','name'=>'Ajman Police','country'=>'UAE','border'=>'border-slate-500/40','services'=>['Traffic Fines'],'link'=>'https://www.ajmanpolice.gov.ae','bg'=>'from-slate-800/40'],
            ['flag'=>'🇦🇪','name'=>'RAK Police','country'=>'UAE','border'=>'border-slate-500/40','services'=>['Traffic Fines'],'link'=>'https://www.rakpolice.gov.ae','bg'=>'from-slate-800/40'],
            ['flag'=>'🇦🇪','name'=>'Fujairah Police','country'=>'UAE','border'=>'border-slate-500/40','services'=>['Traffic Fines'],'link'=>'https://www.fujairahpolice.gov.ae','bg'=>'from-slate-800/40'],
            ['flag'=>'🇦🇪','name'=>'UAQ Police','country'=>'UAE','border'=>'border-slate-500/40','services'=>['Traffic Fines'],'link'=>'https://uaqpolice.gov.ae','bg'=>'from-slate-800/40'],
            ['flag'=>'🇸🇦','name'=>'Saudi Muroor / Absher','country'=>'KSA','border'=>'border-green-500/40','services'=>['Traffic Fines','License Renewal'],'link'=>'https://www.absher.sa','bg'=>'from-green-900/20'],
            ['flag'=>'🇰🇼','name'=>'Kuwait MOI','country'=>'Kuwait','border'=>'border-green-500/40','services'=>['Traffic Fines'],'link'=>'https://www.moi.gov.kw','bg'=>'from-green-900/20'],
            ['flag'=>'🇧🇭','name'=>'Bahrain MOI','country'=>'Bahrain','border'=>'border-red-500/40','services'=>['Traffic Fines'],'link'=>'https://www.bahrain.bh','bg'=>'from-red-900/20'],
            ['flag'=>'🇶🇦','name'=>'Qatar MOI','country'=>'Qatar','border'=>'border-purple-500/40','services'=>['Traffic Fines'],'link'=>'https://www.moi.gov.qa','bg'=>'from-purple-900/20'],
            ['flag'=>'🇴🇲','name'=>'Oman ROP','country'=>'Oman','border'=>'border-red-500/40','services'=>['Traffic Fines'],'link'=>'https://www.rop.gov.om','bg'=>'from-red-900/20'],
        ];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($portalAuthorities as $portal)
            <div class="glass-card p-5 bg-gradient-to-br {{ $portal['bg'] }} to-transparent border {{ $portal['border'] }} group hover:shadow-amber-500/10 hover:shadow-lg transition-all">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <span class="text-2xl">{{ $portal['flag'] }}</span>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mt-1">{{ $portal['country'] }}</p>
                    </div>
                </div>
                <p class="text-sm font-black text-white mb-3 leading-tight">{{ $portal['name'] }}</p>
                <ul class="space-y-1 mb-4">
                    @foreach($portal['services'] as $svc)
                    <li class="flex items-center space-x-1.5">
                        <div class="w-1 h-1 rounded-full bg-amber-400"></div>
                        <span class="text-[10px] text-slate-400 font-medium">{{ $svc }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ $portal['link'] }}" target="_blank" rel="noopener"
                   class="portal-link-btn flex items-center justify-between bg-slate-800 hover:bg-amber-500 hover:text-slate-900 text-amber-400 text-[10px] font-black uppercase tracking-wider px-3 py-2 rounded-lg transition-all group-hover:bg-amber-500 group-hover:text-slate-900">
                    <span>OPEN PORTAL</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ─────────────────────── SECTION 5: FINES TABLE ─────────────────────── --}}
    <div id="fine-records" class="glass-card p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
            <h3 class="text-xs font-black uppercase tracking-widest text-amber-400 border-l-2 border-amber-400 pl-3">📋 Fine Records</h3>
            <div class="flex items-center space-x-3">
                {{-- Filter Tabs --}}
                <div class="flex items-center bg-slate-800 rounded-xl p-1 space-x-1">
                    @foreach(['all'=>'ALL','unpaid'=>'UNPAID','paid'=>'PAID','appealed'=>'DISPUTED'] as $key=>$label)
                    <button x-data @click="$dispatch('filter-fines', '{{ $key }}')"
                        :class="activeFilter === '{{ $key }}' ? 'bg-amber-500 text-slate-900' : 'text-slate-400 hover:text-white'"
                        x-bind:class="$store.fineFilter === '{{ $key }}' ? 'bg-amber-500 text-slate-900' : 'text-slate-400 hover:text-white'"
                        class="text-[9px] font-black uppercase px-3 py-1.5 rounded-lg transition-all">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-800">
                        <th class="py-3 px-3 text-left">#</th>
                        <th class="py-3 px-3 text-left">Vehicle</th>
                        <th class="py-3 px-3 text-left">Authority</th>
                        <th class="py-3 px-3 text-left">Type</th>
                        <th class="py-3 px-3 text-left">Date</th>
                        <th class="py-3 px-3 text-right">Amount</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @forelse($fines as $fine)
                    @php
                        $statusConfig = [
                            'unpaid'           => ['bg'=>'bg-red-500/20','text'=>'text-red-400','border'=>'border-red-500/40','label'=>'UNPAID','pulse'=>true],
                            'paid'             => ['bg'=>'bg-emerald-500/20','text'=>'text-emerald-400','border'=>'border-emerald-500/40','label'=>'PAID','pulse'=>false],
                            'under-processing' => ['bg'=>'bg-amber-500/20','text'=>'text-amber-400','border'=>'border-amber-500/40','label'=>'PROCESSING','pulse'=>false],
                            'appealed'         => ['bg'=>'bg-purple-500/20','text'=>'text-purple-400','border'=>'border-purple-500/40','label'=>'DISPUTED','pulse'=>false],
                            'cancelled'        => ['bg'=>'bg-slate-700/50','text'=>'text-slate-400','border'=>'border-slate-600','label'=>'CANCELLED','pulse'=>false],
                        ];
                        $s = $statusConfig[$fine->status] ?? $statusConfig['unpaid'];
                    @endphp
                    <tr class="fine-row" data-status="{{ $fine->status }}">
                        <td class="py-3 px-3 text-[10px] font-mono text-slate-600">#{{ $fine->id }}</td>
                        <td class="py-3 px-3">
                            <div>
                                <p class="text-sm font-black text-white uppercase">{{ $fine->vehicle->vehicle_number ?? '—' }}</p>
                                @if($fine->vehicle?->plate_number)
                                <span class="font-mono text-[10px] text-slate-500">{{ $fine->vehicle->plate_number }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="text-xs font-bold text-slate-300">{{ $fine->authority }}</span>
                        </td>
                        <td class="py-3 px-3">
                            <span class="text-[10px] text-slate-400 font-medium">{{ $fine->fine_type ?? '—' }}</span>
                        </td>
                        <td class="py-3 px-3">
                            <p class="text-[10px] text-slate-300 font-bold">{{ $fine->fine_date?->format('d M Y') }}</p>
                            @if($fine->due_date)
                            <p class="text-[9px] text-slate-600">Due: {{ $fine->due_date->format('d M Y') }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-right">
                            <span class="text-sm font-black {{ $fine->status === 'paid' ? 'text-emerald-400' : 'text-red-400' }}">
                                AED {{ number_format($fine->amount, 0) }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase border {{ $s['bg'] }} {{ $s['text'] }} {{ $s['border'] }} {{ $s['pulse'] ? 'pulse-red' : '' }}">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right">
                            <div class="flex items-center justify-end space-x-1.5">
                                @if($fine->status === 'unpaid')
                                <form method="POST" action="{{ route('company.fines.checker.paid', $fine) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-2 py-1 bg-emerald-900/50 hover:bg-emerald-800/60 text-emerald-400 text-[9px] font-black uppercase rounded-lg border border-emerald-500/30 transition-all">PAID</button>
                                </form>
                                <form method="POST" action="{{ route('company.fines.checker.dispute', $fine) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-2 py-1 bg-purple-900/50 hover:bg-purple-800/60 text-purple-400 text-[9px] font-black uppercase rounded-lg border border-purple-500/30 transition-all">DISPUTE</button>
                                </form>
                                @endif
                                <a href="{{ route('company.fines.index') }}" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-400 text-[9px] font-black uppercase rounded-lg transition-all">VIEW</a>
                                <form method="POST" action="{{ route('company.fines.checker.destroy', $fine) }}" onsubmit="return confirm('Delete this fine record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-red-900/30 hover:bg-red-900/60 text-red-500 text-[9px] font-black uppercase rounded-lg border border-red-500/20 transition-all">DEL</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="text-slate-600">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">No fine records found</p>
                                <p class="text-xs text-slate-600 mt-1">Use the form below to record a fine manually</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─────────────────────── SECTION 6: SLIDE-IN PANEL ─────────────────────── --}}
    {{-- Overlay --}}
    <div x-show="openPanel" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="openPanel = false"
         class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40"
         style="display:none;"></div>

    {{-- Slide Panel --}}
    <div x-show="openPanel"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
         class="slide-panel fixed right-0 top-0 h-full w-full sm:w-[480px] z-50 overflow-y-auto"
         style="display:none;">
        <div class="min-h-full p-6" style="background:#0D1628; border-left:1px solid #1F2937;">
            {{-- Panel Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-white">✏️ Record Fine Manually</h3>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1">Log a traffic violation directly into the system</p>
                </div>
                <button @click="openPanel = false" class="w-8 h-8 flex items-center justify-center bg-slate-800 hover:bg-slate-700 text-slate-400 rounded-lg transition-all text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('company.fines.checker.store') }}" class="space-y-4">
                @csrf
                {{-- Vehicle --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Vehicle <span class="text-red-400">*</span></label>
                    <select name="vehicle_id" x-model="recordVehicleId" @change="onVehicleSelect($event)" required class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none">
                        <option value="">— Select Vehicle —</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" data-source="{{ $v->plate_source }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->vehicle_number }} {{ $v->plate_number_dp ? '— '.$v->plate_number_dp : ($v->plate_number ? '— '.$v->plate_number : '') }}
                        </option>
                        @endforeach
                    </select>
                    @error('vehicle_id') <p class="text-[10px] text-red-400 mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Authority --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Traffic Authority <span class="text-red-400">*</span></label>
                    <select name="authority" x-model="recordAuthority" required class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none">
                        <option value="">— Select Authority —</option>
                        <optgroup label="UAE — Emirates">
                            <option value="Dubai Police" {{ old('authority')=='Dubai Police'?'selected':'' }}>Dubai Police</option>
                            <option value="Dubai RTA" {{ old('authority')=='Dubai RTA'?'selected':'' }}>Dubai RTA</option>
                            <option value="Abu Dhabi Police" {{ old('authority')=='Abu Dhabi Police'?'selected':'' }}>Abu Dhabi Police</option>
                            <option value="Sharjah Police" {{ old('authority')=='Sharjah Police'?'selected':'' }}>Sharjah Police</option>
                            <option value="Ajman Police" {{ old('authority')=='Ajman Police'?'selected':'' }}>Ajman Police</option>
                            <option value="RAK Police" {{ old('authority')=='RAK Police'?'selected':'' }}>RAK Police</option>
                            <option value="Fujairah Police" {{ old('authority')=='Fujairah Police'?'selected':'' }}>Fujairah Police</option>
                            <option value="UAQ Police" {{ old('authority')=='UAQ Police'?'selected':'' }}>UAQ Police</option>
                        </optgroup>
                        <optgroup label="Gulf Region">
                            <option value="Saudi Muroor / Absher" {{ old('authority')=='Saudi Muroor / Absher'?'selected':'' }}>Saudi Muroor / Absher</option>
                            <option value="Kuwait MOI" {{ old('authority')=='Kuwait MOI'?'selected':'' }}>Kuwait MOI</option>
                            <option value="Bahrain MOI" {{ old('authority')=='Bahrain MOI'?'selected':'' }}>Bahrain MOI</option>
                            <option value="Qatar MOI" {{ old('authority')=='Qatar MOI'?'selected':'' }}>Qatar MOI</option>
                            <option value="Oman ROP" {{ old('authority')=='Oman ROP'?'selected':'' }}>Oman ROP</option>
                        </optgroup>
                    </select>
                    @error('authority') <p class="text-[10px] text-red-400 mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Fine Number + Type --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Fine / Ticket # <span class="text-red-400">*</span></label>
                        <input type="text" name="fine_number" value="{{ old('fine_number') }}" required placeholder="TKT-00001"
                            class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-mono font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none placeholder-slate-600">
                        @error('fine_number') <p class="text-[10px] text-red-400 mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Violation Type</label>
                        <select name="fine_type" class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none">
                            <option value="">— Type —</option>
                            <option value="Speeding">Speeding</option>
                            <option value="Signal Jumping">Signal Jumping</option>
                            <option value="Illegal Parking">Illegal Parking</option>
                            <option value="Using Mobile">Using Mobile</option>
                            <option value="Seat Belt">Seat Belt</option>
                            <option value="Wrong Lane">Wrong Lane</option>
                            <option value="Vehicle Condition">Vehicle Condition</option>
                            <option value="Registration Expired">Registration Expired</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Fine Date <span class="text-red-400">*</span></label>
                        <input type="date" name="fine_date" value="{{ old('fine_date', date('Y-m-d')) }}" required
                            class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none">
                        @error('fine_date') <p class="text-[10px] text-red-400 mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                            class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Penalty Amount (AED) <span class="text-red-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-black text-amber-400 bg-slate-800 border border-slate-700 px-3 py-2.5 rounded-xl">AED</span>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required placeholder="0.00"
                            class="flex-1 bg-slate-800 border border-slate-700 text-amber-400 text-sm font-black rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none placeholder-slate-600">
                    </div>
                    @error('amount') <p class="text-[10px] text-red-400 mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Responsibility --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Responsibility</label>
                    <div class="flex items-center space-x-4">
                        @foreach(['company'=>'Company','driver'=>'Driver','customer'=>'Customer'] as $val=>$label)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="responsible_type" value="{{ $val }}" {{ old('responsible_type','company')===$val?'checked':'' }}
                                class="w-4 h-4 accent-amber-400">
                            <span class="text-xs font-bold text-slate-300">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Notes</label>
                    <textarea name="description" rows="2" placeholder="Optional notes..."
                        class="w-full bg-slate-800 border border-slate-700 text-white text-sm font-medium rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none placeholder-slate-600 resize-none">{{ old('description') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center space-x-3 pt-2">
                    <button type="button" @click="openPanel = false"
                        class="flex-1 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-black text-xs uppercase rounded-xl transition-all">
                        CANCEL
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black text-xs uppercase rounded-xl shadow-lg shadow-amber-500/30 transition-all">
                        SAVE FINE RECORD
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
@php
$authorityLinksJs = [
    'DXB' => 'https://www.dubaipolice.gov.ae/app/services/fine-payment/search',
    'RTA' => 'https://www.rta.ae',
    'AUH' => 'https://www.adpolice.gov.ae/en/services/traffic-services/traffic-fines-inquiry',
    'SHJ' => 'https://www.sharjahpolice.gov.ae',
    'AJM' => 'https://www.ajmanpolice.gov.ae',
    'RAK' => 'https://www.rakpolice.gov.ae',
    'FUJ' => 'https://www.fujairahpolice.gov.ae',
    'UAQ' => 'https://uaqpolice.gov.ae',
    'SAR' => 'https://www.absher.sa',
    'KWT' => 'https://www.moi.gov.kw',
    'BAH' => 'https://www.bahrain.bh',
    'QAT' => 'https://www.moi.gov.qa',
    'OMA' => 'https://www.rop.gov.om',
];
$authorityNamesJs = [
    'DXB'=>'Dubai Police','RTA'=>'Dubai RTA','AUH'=>'Abu Dhabi Police',
    'SHJ'=>'Sharjah Police','AJM'=>'Ajman Police','RAK'=>'RAK Police',
    'FUJ'=>'Fujairah Police','UAQ'=>'UAQ Police','SAR'=>'Saudi Muroor / Absher',
    'KWT'=>'Kuwait MOI','BAH'=>'Bahrain MOI','QAT'=>'Qatar MOI','OMA'=>'Oman ROP',
];
$plateHintsJs = [
    'DXB'=>'Dubai: Letter(s) + Number (e.g. A 12345 or AB 1234)',
    'RTA'=>'Dubai RTA: Enter plate as registered on vehicle',
    'AUH'=>'Abu Dhabi: Number only (e.g. 12345)',
    'SHJ'=>'Sharjah: Letter + Number (e.g. S 1234)',
    'AJM'=>'Ajman: Letter + Number',
    'RAK'=>'RAK: Letter + Number',
    'FUJ'=>'Fujairah: Letter + Number',
    'UAQ'=>'UAQ: Letter + Number',
    'SAR'=>'Saudi: 3 Letters + 4 Numbers (e.g. ABC 1234)',
    'KWT'=>'Kuwait: 2 Digits + 4-5 Numbers',
    'BAH'=>'Bahrain: Numbers only',
    'QAT'=>'Qatar: Numbers only',
    'OMA'=>'Oman: A + 4-5 Numbers',
];
$plateCodesJs = [
    'DXB' => array_merge(range('A','Z'), ['AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ']),
    'AUH' => [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],
    'RTA' => range('A','Z'),
    'DEFAULT' => array_merge(range('A','Z'), [1,2,3,4,5]),
];
@endphp

const authorityLinks = @json($authorityLinksJs);
const authorityNames = @json($authorityNamesJs);
const plateHints     = @json($plateHintsJs);
const plateCodes     = @json($plateCodesJs);

function fineChecker() {
    return {
        openPanel: {{ $errors->any() ? 'true' : 'false' }},
        selectedAuthority: '',
        selectedAuthorityName: '',
        plateCode: '',
        plateNumber: '',
        officialLink: '#',
        plateHint: '',
        plateCodes: [],
        
        recordVehicleId: '{{ old("vehicle_id") }}',
        recordAuthority: '{{ old("authority") }}',

        onVehicleSelect(e) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const source = selectedOption.getAttribute('data-source');
            if (source) {
                const authorityMap = {
                    'Dubai': 'Dubai Police',
                    'Abu Dhabi': 'Abu Dhabi Police',
                    'Sharjah': 'Sharjah Police',
                    'Ajman': 'Ajman Police',
                    'RAK': 'RAK Police',
                    'Fujairah': 'Fujairah Police',
                    'UAQ': 'UAQ Police',
                    'Saudi Arabia': 'Saudi Muroor / Absher',
                    'Kuwait': 'Kuwait MOI',
                    'Bahrain': 'Bahrain MOI',
                    'Qatar': 'Qatar MOI',
                    'Oman': 'Oman ROP'
                };
                if (authorityMap[source]) {
                    this.recordAuthority = authorityMap[source];
                }
            }
        },

        selectAuthority(code, name) {
            this.selectedAuthority = code;
            this.selectedAuthorityName = name;
            this.officialLink = authorityLinks[code] || '#';
            this.plateHint   = plateHints[code] || '';
            this.plateCodes  = plateCodes[code] || plateCodes['DEFAULT'];
            this.plateCode   = '';
        },

        formatNum(n) {
            return n.toLocaleString('en-AE');
        },

        countUp(target) {
            return target; // Static for now; animated via CSS
        },

        initCounters() {
            // Animate stat cards on load
            document.querySelectorAll('.stat-value').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(8px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.5s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, 200);
            });

            // Open panel if validation errors
            if (this.openPanel) {
                // already true
            }
        }
    };
}
</script>
@endpush
