@extends('layouts.company')

@section('title', 'Fleet Control Center — OwnBus')

@section('header_title')
<div class="flex items-center space-x-3">
    <div class="relative">
        <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></div>
        <div class="absolute inset-0 w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping opacity-50"></div>
    </div>
    <h1 class="text-xl font-bold tracking-tight uppercase" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 0.12em;">Fleet Operations Control</h1>
</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* ══════════════════════════════════════════
       DESERT NIGHT PREMIUM — Color System
    ══════════════════════════════════════════ */
    :root {
        --bg-primary:    #0A0F1E;
        --bg-card:       #111827;
        --bg-card-hover: #1F2937;
        --border-color:  #1F2937;

        --color-gold:    #F59E0B;
        --color-emerald: #10B981;
        --color-blue:    #3B82F6;
        --color-red:     #EF4444;
        --color-purple:  #8B5CF6;
        --color-orange:  #F97316;
        --text-primary:  #F9FAFB;
        --text-secondary:#9CA3AF;
    }
    body { font-family: 'DM Sans', sans-serif; background-color: var(--bg-primary); }
    h1, h2, h3, .bebas { font-family: 'Bebas Neue', sans-serif; }

    /* ── Base Card ── */
    .glass-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        transition: background 0.2s ease, border-color 0.2s ease;
    }
    .glass-card:hover { background: var(--bg-card-hover); }
    .glass-card-light {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
    }

    /* ── Accent Left-Border Stat Cards ── */
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-left-width: 4px;
        border-radius: 1rem;
        padding: 1rem;
        position: relative;
        overflow: hidden;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .stat-card:hover { background: var(--bg-card-hover); transform: translateY(-2px); }
    .stat-card-emerald { border-left-color: #10B981; }
    .stat-card-gold    { border-left-color: #F59E0B; }
    .stat-card-blue    { border-left-color: #3B82F6; }
    .stat-card-purple  { border-left-color: #8B5CF6; }
    .stat-card-orange  { border-left-color: #F97316; }
    .stat-card-red     { border-left-color: #EF4444; }
    .stat-card-green   { border-left-color: #10B981; }
    .stat-card-amber   { border-left-color: #F59E0B; }

    /* ── Icon Circles ── */
    .icon-emerald { background: rgba(16,185,129,0.15); color: #10B981; }
    .icon-gold    { background: rgba(245,158,11,0.15);  color: #F59E0B; }
    .icon-blue    { background: rgba(59,130,246,0.15);  color: #3B82F6; }
    .icon-purple  { background: rgba(139,92,246,0.15);  color: #8B5CF6; }
    .icon-orange  { background: rgba(249,115,22,0.15);  color: #F97316; }
    .icon-red     { background: rgba(239,68,68,0.15);   color: #EF4444; }

    /* ── Glow Effects ── */
    .glow-emerald { box-shadow: 0 4px 20px rgba(16,185,129,0.12); }
    .glow-gold    { box-shadow: 0 4px 20px rgba(245,158,11,0.12); }
    .glow-red     { box-shadow: 0 4px 20px rgba(239,68,68,0.12); }
    .glow-blue    { box-shadow: 0 4px 20px rgba(59,130,246,0.12); }

    /* ── Animated Stat Counters ── */
    @keyframes count-up {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-value { animation: count-up 0.6s ease forwards; }

    /* ── Page Load Stagger ── */
    @keyframes fade-slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stagger-1 { animation: fade-slide-up 0.45s ease 0.05s both; }
    .stagger-2 { animation: fade-slide-up 0.45s ease 0.12s both; }
    .stagger-3 { animation: fade-slide-up 0.45s ease 0.20s both; }
    .stagger-4 { animation: fade-slide-up 0.45s ease 0.28s both; }
    .stagger-5 { animation: fade-slide-up 0.45s ease 0.36s both; }
    .stagger-6 { animation: fade-slide-up 0.45s ease 0.44s both; }
    .stagger-7 { animation: fade-slide-up 0.45s ease 0.52s both; }

    /* ── Action Pills (solid colored) ── */
    .action-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.625rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .action-pill:hover { filter: brightness(1.12); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
    .action-pill:hover .pill-icon { transform: scale(1.15) rotate(-5deg); }
    .pill-icon { transition: transform 0.2s ease; display: inline-block; }

    .pill-emerald { background: #10B981; box-shadow: 0 4px 14px rgba(16,185,129,0.35); }
    .pill-blue    { background: #3B82F6; box-shadow: 0 4px 14px rgba(59,130,246,0.35); }
    .pill-purple  { background: #8B5CF6; box-shadow: 0 4px 14px rgba(139,92,246,0.35); }
    .pill-gold    { background: #F59E0B; box-shadow: 0 4px 14px rgba(245,158,11,0.35); }
    .pill-orange  { background: #F97316; box-shadow: 0 4px 14px rgba(249,115,22,0.35); }
    .pill-red     { background: #EF4444; box-shadow: 0 4px 14px rgba(239,68,68,0.35); }

    /* ── Event Stream ── */
    @keyframes event-slide {
        from { opacity: 0; transform: translateX(-10px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .event-item { animation: event-slide 0.35s ease both; }

    /* ── Gauge Animation ── */
    @keyframes gauge-fill { from { stroke-dashoffset: 502; } }
    .gauge-animated { animation: gauge-fill 1.2s ease-out 0.3s both; }

    /* ── System Stable Banner ── */
    .banner-stable {
        background: linear-gradient(135deg, #064E3B, #065F46);
        border-left: 4px solid #10B981;
        border-top: 1px solid rgba(16,185,129,0.2);
        border-right: 1px solid rgba(16,185,129,0.2);
        border-bottom: 1px solid rgba(16,185,129,0.2);
        border-radius: 1rem;
    }
    .banner-critical {
        background: linear-gradient(135deg, #450a0a, #7f1d1d);
        border-left: 4px solid #EF4444;
        border-top: 1px solid rgba(239,68,68,0.25);
        border-right: 1px solid rgba(239,68,68,0.25);
        border-bottom: 1px solid rgba(239,68,68,0.25);
        border-radius: 1rem;
    }

    /* ── Scrollbar ── */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #1F2937; border-radius: 99px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #374151; }

    /* ── Live Status Pulse ── */
    @keyframes live-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
        50%       { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
    }
    .live-dot { animation: live-pulse 1.8s infinite; }

    /* ── Map Legend ── */
    .map-legend {
        background: rgba(17, 24, 39, 0.92);
        backdrop-filter: blur(12px);
        border: 1px solid #1F2937;
    }

    /* ── Section Headers ── */
    .section-header {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 1.25rem; margin-top: 2.5rem;
    }
    .section-header::after {
        content: ''; flex: 1; height: 1px;
        background: linear-gradient(to right, #1F2937, transparent);
    }

    /* ── Risk Badges ── */
    .risk-low      { background: rgba(16,185,129,0.12);  color: #10B981; border: 1px solid rgba(16,185,129,0.3); }
    .risk-medium   { background: rgba(245,158,11,0.12);  color: #F59E0B; border: 1px solid rgba(245,158,11,0.3); }
    .risk-high     { background: rgba(239,68,68,0.12);   color: #EF4444; border: 1px solid rgba(239,68,68,0.3); }
    .risk-critical { background: rgba(220,38,38,0.18);   color: #EF4444; border: 1px solid rgba(220,38,38,0.35); animation: risk-flash 1s infinite; }
    @keyframes risk-flash { 0%,100%{opacity:1} 50%{opacity:.55} }

    /* ── Progress Ring ── */
    .ring-track { color: #1F2937; }
    .ring-fill  { transition: stroke-dashoffset 1s ease 0.3s; }
</style>
@endpush

@section('content')
<div x-data="{
        revView: 'monthly',
        darkMode: true,
        mapExpanded: false,
        activeSection: 'executive',
        now: new Date()
     }" class="space-y-0">

    {{-- ═══════════════════════════════════════════════════════
         ZONE 0: TOP CONTROL BAR (View Toggle + Context)
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between mb-6 stagger-1">
        <div class="flex items-center space-x-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ now()->format('D, d M Y • H:i') }} GST</span>
        </div>
        <div class="flex items-center space-x-3">
            {{-- View Toggle --}}
            <div class="inline-flex items-center p-1 bg-slate-800/80 border border-slate-700/50 rounded-xl shadow-sm" id="view-toggle-container">
                <button id="btn-exec-view"
                    class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg bg-slate-700 text-white shadow-sm transition-all">
                    Executive
                </button>
                <button id="btn-ops-view"
                    class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg text-slate-400 hover:text-white transition-all">
                    Operations
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         ZONE 1: HERO STATS BAR — Glassmorphism KPIs
    ═══════════════════════════════════════════════════════ --}}
    @php
        $riskScore = $data['risk_score']['score'] ?? 0;
        $riskColor = $riskScore < 50 ? 'emerald' : ($riskScore < 75 ? 'amber' : 'rose');
        $riskLabel = $riskScore < 50 ? 'LOW' : ($riskScore < 75 ? 'MEDIUM' : 'HIGH');
        $maintenanceCount = $data['kpis']['vehicles_in_maintenance'];
        $outstandingAmt = $data['kpis']['outstanding_payments'];
    @endphp

    {{-- ── HERO STAT CARDS — Desert Night Premium ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-3 mb-6 stagger-2">

        {{-- 1. Fleet Status → Emerald --}}
        <div class="stat-card stat-card-emerald col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center icon-emerald text-sm">🚌</span>
                <p class="text-[9px] font-black uppercase tracking-widest" style="color:var(--text-secondary)">Fleet Status</p>
            </div>
            <div class="flex items-end gap-1.5">
                <span class="text-2xl font-black stat-value" style="color:var(--text-primary)">{{ $data['kpis']['active_rentals'] }}</span>
                <span class="text-xs mb-0.5 font-medium" style="color:var(--text-secondary)">/&nbsp;{{ $data['kpis']['total_vehicles'] }}</span>
            </div>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="w-1.5 h-1.5 rounded-full live-dot" style="background:#10B981"></span>
                <span class="text-[9px] font-bold uppercase" style="color:#10B981">Active Buses</span>
            </div>
        </div>

        {{-- 2. MTD Revenue → Gold --}}
        <div class="stat-card stat-card-gold col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center icon-gold text-sm">💰</span>
                <p class="text-[9px] font-black uppercase tracking-widest" style="color:var(--text-secondary)">MTD Revenue</p>
            </div>
            <span class="text-xl font-black stat-value" style="color:#F59E0B">AED&nbsp;{{ number_format($data['kpis']['revenue_this_month'], 0) }}</span>
            <div class="flex items-center gap-1 mt-2">
                <svg class="w-3 h-3" style="color:#10B981" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                <span class="text-[9px] font-bold uppercase" style="color:#10B981">Trending Up</span>
            </div>
        </div>

        {{-- 3. Utilization → Blue --}}
        <div class="stat-card stat-card-blue col-span-1">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center icon-blue text-sm">📊</span>
                <p class="text-[9px] font-black uppercase tracking-widest" style="color:var(--text-secondary)">Utilization</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative w-10 h-10 shrink-0">
                    <svg class="w-10 h-10 -rotate-90" viewBox="0 0 40 40">
                        <circle cx="20" cy="20" r="16" stroke-width="3" fill="transparent" stroke="#1F2937"/>
                        <circle cx="20" cy="20" r="16" stroke-width="3" fill="transparent"
                            stroke="#3B82F6"
                            stroke-dasharray="100.5"
                            stroke-dashoffset="{{ 100.5 - (100.5 * $data['charts']['fleet_utilization'] / 100) }}"
                            class="ring-fill"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-[8px] font-black" style="color:var(--text-primary)">{{ $data['charts']['fleet_utilization'] }}%</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-black stat-value" style="color:var(--text-primary)">{{ $data['charts']['fleet_utilization'] }}%</span>
                    <p class="text-[9px] font-medium" style="color:var(--text-secondary)">deployed</p>
                </div>
            </div>
        </div>

        {{-- 4. Risk Index → Dynamic --}}
        @php
            $riskAccent = $riskColor === 'rose' ? '#EF4444' : ($riskColor === 'amber' ? '#F59E0B' : '#10B981');
            $riskBorder = $riskColor === 'rose' ? 'stat-card-red' : ($riskColor === 'amber' ? 'stat-card-amber' : 'stat-card-green');
            $riskIcon   = $riskColor === 'rose' ? 'icon-red' : ($riskColor === 'amber' ? 'icon-gold' : 'icon-emerald');
        @endphp
        <div class="stat-card {{ $riskBorder }} col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center {{ $riskIcon }} text-sm">
                    {{ $riskColor === 'rose' ? '🔴' : ($riskColor === 'amber' ? '🟡' : '🟢') }}
                </span>
                <p class="text-[9px] font-black uppercase tracking-widest" style="color:var(--text-secondary)">Risk Index</p>
            </div>
            <span class="text-xl font-black stat-value" style="color:{{ $riskAccent }}">{{ $riskLabel }}</span>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="w-1.5 h-1.5 rounded-full {{ $riskColor === 'rose' ? 'animate-pulse' : '' }}" style="background:{{ $riskAccent }}"></span>
                <span class="text-[9px] font-bold uppercase" style="color:{{ $riskAccent }}">Score: {{ $riskScore }}</span>
            </div>
        </div>

        {{-- 5. VAT Payable → Purple --}}
        <div class="stat-card stat-card-purple col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center icon-purple text-sm">🧾</span>
                <p class="text-[9px] font-black uppercase tracking-widest" style="color:var(--text-secondary)">VAT Payable</p>
            </div>
            <span class="text-lg font-black stat-value" style="color:#8B5CF6">AED {{ number_format($data['vat_summary']['net_vat_payable'] ?? 0, 0) }}</span>
            <p class="text-[9px] font-medium mt-2 uppercase" style="color:var(--text-secondary)">{{ $data['vat_summary']['quarter_label'] ?? 'Q2 2024' }}</p>
        </div>

        {{-- 6. In Maintenance → Orange --}}
        <div class="stat-card stat-card-orange col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center icon-orange text-sm">🔧</span>
                <p class="text-[9px] font-black uppercase tracking-widest" style="color:var(--text-secondary)">In Maintenance</p>
            </div>
            <span class="text-2xl font-black stat-value" style="color:{{ $maintenanceCount > 0 ? '#F97316' : 'var(--text-primary)' }}">
                {{ $maintenanceCount }}
            </span>
            @if($maintenanceCount > 0)
            <div class="flex items-center gap-1.5 mt-2">
                <svg class="w-3 h-3" style="color:#F97316" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" /></svg>
                <span class="text-[9px] font-bold uppercase" style="color:#F97316">Needs Attention</span>
            </div>
            @else
            <p class="text-[9px] font-bold mt-2 uppercase" style="color:#10B981">All Clear ✓</p>
            @endif
        </div>

        {{-- 7. Outstanding → Red --}}
        <div class="stat-card stat-card-red col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center icon-red text-sm">⏰</span>
                <p class="text-[9px] font-black uppercase tracking-widest" style="color:var(--text-secondary)">Outstanding</p>
            </div>
            <span class="text-lg font-black stat-value" style="color:{{ $outstandingAmt > 0 ? '#EF4444' : 'var(--text-primary)' }}">
                AED {{ number_format($outstandingAmt, 0) }}
            </span>
            @if($outstandingAmt > 0)
            <div class="flex items-center gap-1.5 mt-2">
                <span class="relative flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background:#EF4444"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5" style="background:#EF4444"></span>
                </span>
                <span class="text-[9px] font-bold uppercase" style="color:#EF4444">Collection Due</span>
            </div>
            @else
            <p class="text-[9px] font-bold mt-2 uppercase" style="color:#10B981">Collected ✓</p>
            @endif
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════
         ZONE 2: QUICK ACTIONS — Animated Pill Buttons
    ═══════════════════════════════════════════════════════ --}}
    {{-- QUICK ACTIONS — Solid Color Pill Buttons --}}
    <div class="stagger-3 mb-6">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('company.fleet.create') }}" class="action-pill pill-emerald">
                <span class="pill-icon">🚌</span> Add Vehicle
            </a>
            <a href="{{ route('company.rentals.create') }}" class="action-pill pill-blue">
                <span class="pill-icon">📋</span> Create Rental
            </a>
            <a href="{{ route('company.customers.create') }}" class="action-pill pill-purple">
                <span class="pill-icon">👤</span> Add Customer
            </a>
            <a href="{{ route('company.finance.invoices') }}" class="action-pill pill-gold">
                <span class="pill-icon">💳</span> Record Payment
            </a>
            <a href="{{ route('company.kanban.index') }}" class="action-pill pill-orange">
                <span class="pill-icon">👨‍✈️</span> Assign Driver
            </a>
            <a href="{{ route('company.fines.create') }}" class="action-pill pill-red">
                <span class="pill-icon">🚦</span> Record Fine
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         ZONE 3: ALERT WALL + HEALTH GAUGE
    ═══════════════════════════════════════════════════════ --}}
    @php
        $hasCriticalRisks = collect($data['risks'])->where('severity', 'high')->count() > 0;
        $criticalRisksCount = collect($data['risks'])->where('severity', 'high')->count();
        $efficiencyScore = $data['efficiency']['score'] ?? 80;
        $riskScoreVal = $data['risk_score']['score'] ?? 20;
        $healthScore = max(0, min(100, round(($efficiencyScore + (100 - $riskScoreVal)) / 2)));
        $healthColor = $healthScore >= 75 ? 'emerald' : ($healthScore >= 50 ? 'amber' : 'rose');
        $healthStatus = $healthScore >= 75 ? 'Healthy' : ($healthScore >= 50 ? 'Monitor' : 'Critical');
        $gaugeCircumference = 502;
        $gaugeDashoffset = $gaugeCircumference - ($gaugeCircumference * $healthScore / 100);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6 stagger-4">
        {{-- Alert Wall --}}
        <div class="lg:col-span-2">
            @if($hasCriticalRisks)
            <div class="relative overflow-hidden rounded-2xl p-6 h-full flex items-center justify-between group cursor-pointer"
                 style="background: linear-gradient(135deg, rgba(255,71,87,0.12), rgba(220,38,38,0.08)); border: 1px solid rgba(255,71,87,0.25);"
                 onclick="document.getElementById('riskCenterModal').showModal()">
                <div class="absolute inset-0 bg-gradient-to-r from-red-950/30 to-transparent pointer-events-none"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-red-500/10 blur-3xl -mr-32 -mt-32 pointer-events-none transition-transform group-hover:scale-125 duration-700"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        <h2 class="text-xl font-black text-white uppercase tracking-wide bebas" style="letter-spacing:.1em">⚠ Immediate Action Required</h2>
                    </div>
                    <p class="text-red-300 text-sm font-semibold">{{ $criticalRisksCount }} Critical System Risk{{ $criticalRisksCount > 1 ? 's' : '' }} Detected</p>
                </div>
                <button class="relative z-10 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest text-white transition-all shrink-0"
                        style="background: rgba(255,71,87,0.3); border: 1px solid rgba(255,71,87,0.4);"
                        onmouseover="this.style.background='rgba(255,71,87,0.5)'"
                        onmouseout="this.style.background='rgba(255,71,87,0.3)'">
                    View All &rarr;
                </button>
            </div>
            @else
            <div class="banner-stable relative overflow-hidden p-6 h-full flex items-center justify-between group cursor-pointer"
                 onclick="document.getElementById('riskCenterModal').showModal()">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-3 h-3 rounded-full live-dot" style="background:#10B981; box-shadow: 0 0 10px #10B981"></div>
                        <h2 class="text-xl font-black text-white uppercase tracking-wide bebas" style="letter-spacing:.1em">System Stable</h2>
                    </div>
                    <p class="text-sm font-medium" style="color:#6EE7B7">No Critical Risks · Operations running efficiently.</p>
                </div>
                <button class="relative z-10 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all shrink-0"
                        style="background: rgba(16,185,129,0.15); border: 1px solid #10B981; color: #10B981;"
                        onmouseover="this.style.background='rgba(16,185,129,0.28)'"
                        onmouseout="this.style.background='rgba(16,185,129,0.15)'">
                    View Center &rarr;
                </button>
            </div>
            @endif
        </div>

        {{-- Company Health Gauge --}}
        @php
            $gaugeStroke = $healthScore >= 75 ? '#10B981' : ($healthScore >= 50 ? '#F59E0B' : '#EF4444');
        @endphp
        <div class="glass-card rounded-2xl p-6 flex flex-col items-center justify-center relative overflow-hidden">
            <p class="text-[10px] font-black uppercase tracking-widest mb-4 z-10" style="color:var(--text-secondary)">Company Health</p>

            <div class="relative w-36 h-36 mb-4 z-10">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 192 192">
                    <circle cx="96" cy="96" r="80" stroke-width="12" fill="transparent" stroke="#1F2937"/>
                    <circle cx="96" cy="96" r="80" stroke-width="12" fill="transparent" stroke-linecap="round"
                        stroke="{{ $gaugeStroke }}"
                        stroke-dasharray="{{ $gaugeCircumference }}"
                        stroke-dashoffset="{{ $gaugeDashoffset }}"
                        class="gauge-animated"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-4xl font-black text-white leading-none">{{ $healthScore }}</span>
                    <span class="text-[9px] font-black text-slate-500 uppercase mt-1">/ 100</span>
                </div>
            </div>

            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest z-10
                {{ $healthColor === 'emerald' ? 'risk-low' : ($healthColor === 'amber' ? 'risk-medium' : 'risk-high') }}">
                {{ $healthStatus }}
            </span>

            {{-- Sub-metrics --}}
            @php
                $factors = collect($data['risk_score']['factors'] ?? []);
                $maintOverdue = $factors->firstWhere('label', 'Maintenance Overdue');
                $driverDocs = $factors->firstWhere('label', 'Missing/Expired Driver Documents');
                $arAging = $factors->firstWhere('label', 'Unpaid Invoices (>30 Days)');
            @endphp
            <div class="grid grid-cols-2 gap-2 w-full mt-4 z-10 border-t border-white/5 pt-4">
                <div class="text-center">
                    <span class="text-xs font-black {{ isset($maintOverdue) && $maintOverdue['penalty'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ isset($maintOverdue) ? $maintOverdue['penalty'].'%' : '0%' }}
                    </span>
                    <p class="text-[8px] text-slate-600 uppercase tracking-widest mt-0.5">Maint %</p>
                </div>
                <div class="text-center">
                    <span class="text-xs font-black {{ isset($driverDocs) && $driverDocs['penalty'] > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                        {{ isset($driverDocs) ? $driverDocs['penalty'].'%' : '0%' }}
                    </span>
                    <p class="text-[8px] text-slate-600 uppercase tracking-widest mt-0.5">Driver Docs</p>
                </div>
                <div class="text-center">
                    <span class="text-xs font-black {{ isset($arAging) && $arAging['penalty'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ isset($arAging) ? $arAging['penalty'].'%' : '0%' }}
                    </span>
                    <p class="text-[8px] text-slate-600 uppercase tracking-widest mt-0.5">AR Aging</p>
                </div>
                <div class="text-center">
                    <span class="text-xs font-black text-emerald-400">0%</span>
                    <p class="text-[8px] text-slate-600 uppercase tracking-widest mt-0.5">Fines Ovrd</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Risk Modal --}}
    <dialog id="riskCenterModal" class="backdrop:bg-slate-950/90 bg-transparent rounded-2xl w-full max-w-4xl p-0 m-auto">
        <div style="background: #0F172A; border: 1px solid rgba(255,255,255,0.08);" class="rounded-2xl overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-white/5">
                <h3 class="font-black text-white uppercase tracking-wide bebas text-lg">Risk Control Center</h3>
                <form method="dialog">
                    <button class="text-slate-500 hover:text-white transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </form>
            </div>
            <div class="p-6 max-h-[80vh] overflow-y-auto">
                @include('company.dashboard.partials.risk-center', ['risks' => $data['risks']])
            </div>
        </div>
    </dialog>

    {{-- ═══════════════════════════════════════════════════════
         ZONE 4: LIVE GPS MAP + ACTIVE JOBS SIDEBAR
    ═══════════════════════════════════════════════════════ --}}
    <div class="section-header stagger-5">
        <div class="p-1.5 rounded-lg" style="background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.2);">
            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="text-base font-black text-white uppercase tracking-widest bebas">Live Operations Hub</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6 stagger-5">
        {{-- Map (2/3 width) --}}
        <div id="zone3-map-container"
             class="lg:col-span-2 flex flex-col rounded-2xl overflow-hidden relative transition-all duration-500"
             style="height: 520px; border: 1px solid rgba(255,255,255,0.06); background: #060D1A;">

            {{-- Map Loading Overlay --}}
            <div id="map-loading" class="absolute inset-0 flex flex-col items-center justify-center z-20" style="background: rgba(6,13,26,0.9);">
                <div class="w-12 h-12 rounded-full border-2 border-cyan-500/20 border-t-cyan-500 animate-spin mb-4"></div>
                <p class="text-[10px] font-black text-cyan-400 uppercase tracking-widest animate-pulse">Connecting to GPS Network...</p>
            </div>

            {{-- Map Legend --}}
            <div class="absolute bottom-4 left-4 z-30 map-legend rounded-xl px-4 py-2.5 flex items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 live-dot"></span>
                    <span class="text-[9px] font-bold text-emerald-300 uppercase">Live</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-[9px] font-bold text-red-300 uppercase">Offline</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">No GPS</span>
                </div>
            </div>

            {{-- Expand Button --}}
            <button onclick="toggleMapFullscreen()"
                    class="absolute top-4 right-4 z-30 map-legend rounded-lg px-3 py-2 text-[9px] font-black text-slate-300 uppercase tracking-widest hover:text-white flex items-center gap-1.5 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                Expand Map
            </button>

            <x-dashboard.fleet-map :companyId="$company->id" />
        </div>

        {{-- Active Jobs Sidebar (1/3 width) --}}
        <div class="space-y-4 h-[520px] flex flex-col ops-detail transition-all duration-500">

            {{-- Active Jobs --}}
            <div class="glass-card rounded-2xl p-4 flex-1 flex flex-col min-h-0">
                <div class="flex items-center justify-between mb-4 shrink-0">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Jobs</h3>
                    <a href="{{ route('company.rentals.index') }}" class="text-[9px] font-black text-cyan-400 hover:text-cyan-300 uppercase tracking-widest transition-colors">View All</a>
                </div>
                <div class="overflow-y-auto flex-1 custom-scrollbar space-y-2 pr-1">
                    @forelse($data['active_rentals'] as $rental)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition-colors border border-transparent hover:border-white/5 group">
                        {{-- Driver Avatar --}}
                        <div class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center text-[10px] font-black"
                             style="background: rgba(6,182,212,0.15); color: #67e8f9; border: 1px solid rgba(6,182,212,0.2);">
                            {{ substr($rental->customer->name ?? 'N', 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-bold text-slate-200 truncate">{{ $rental->customer->name ?? 'N/A' }}</p>
                            <p class="text-[9px] text-slate-500 truncate">{{ $rental->vehicle->name ?? 'N/A' }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($rental->status === 'active')
                            <span class="w-2 h-2 rounded-full bg-emerald-500 live-dot"></span>
                            @else
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-[10px] text-slate-600 uppercase tracking-widest">No active deployments</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Live Event Stream --}}
            <div class="glass-card rounded-2xl p-4 flex-1 flex flex-col min-h-0">
                <div class="flex items-center justify-between mb-4 shrink-0">
                    <div class="flex items-center gap-2">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Event Stream</h3>
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                    </div>
                </div>
                <div class="overflow-y-auto flex-1 custom-scrollbar space-y-2 pr-1">
                    @forelse(collect($data['timeline'])->take(8) as $idx => $event)
                    @php
                        $actionLower = strtolower($event->action);
                        if (str_contains($actionLower, 'payment') || str_contains($actionLower, 'paid')) {
                            $evIcon = '💰'; $evColor = 'text-amber-400'; $evBg = 'rgba(245,158,11,0.12)';
                        } elseif (str_contains($actionLower, 'fine')) {
                            $evIcon = '🚦'; $evColor = 'text-red-400'; $evBg = 'rgba(255,71,87,0.12)';
                        } elseif (str_contains($actionLower, 'vehicle') || str_contains($actionLower, 'added')) {
                            $evIcon = '🚌'; $evColor = 'text-blue-400'; $evBg = 'rgba(59,130,246,0.12)';
                        } elseif (str_contains($actionLower, 'created') || str_contains($actionLower, 'rental')) {
                            $evIcon = '📋'; $evColor = 'text-emerald-400'; $evBg = 'rgba(0,200,150,0.12)';
                        } else {
                            $evIcon = '⚡'; $evColor = 'text-cyan-400'; $evBg = 'rgba(6,182,212,0.12)';
                        }
                    @endphp
                    <div class="event-item flex items-start gap-2.5 p-2.5 rounded-xl" style="background: {{ $evBg }}; border: 1px solid rgba(255,255,255,0.04); animation-delay: {{ $idx * 60 }}ms">
                        <span class="text-sm mt-0.5 shrink-0">{{ $evIcon }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-bold {{ $evColor }} truncate">{{ $event->action }}</p>
                            <p class="text-[9px] text-slate-600 mt-0.5">{{ \Carbon\Carbon::parse($event->occurred_at)->diffForHumans(null, true, true) }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-[10px] text-slate-600 uppercase tracking-widest">System quiet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         ZONE 5: DOCUMENT EXPIRY TRACKER
    ═══════════════════════════════════════════════════════ --}}
    <div class="section-header stagger-6">
        <div class="p-1.5 rounded-lg" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h2 class="text-base font-black text-white uppercase tracking-widest bebas">Document Expiry Tracker</h2>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden stagger-6 mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <th class="py-3.5 px-6 text-[9px] font-black text-slate-500 uppercase tracking-widest">Vehicle</th>
                        <th class="py-3.5 px-6 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Registration</th>
                        <th class="py-3.5 px-6 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Insurance</th>
                        <th class="py-3.5 px-6 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Inspection</th>
                        <th class="py-3.5 px-6 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Route Permit</th>
                        <th class="py-3.5 px-6 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['expiring_vehicles'] as $vehicle)
                    <tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-[10px] font-black text-cyan-300"
                                     style="background: rgba(6,182,212,0.12); border: 1px solid rgba(6,182,212,0.2);">
                                    {{ substr($vehicle->vehicle_number, -4) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-200">{{ $vehicle->name }}</p>
                                    <p class="text-[9px] text-slate-600 uppercase font-medium">{{ $vehicle->vehicle_number }}</p>
                                </div>
                            </div>
                        </td>

                        @php
                        $docBadge = function($date) {
                            if (!$date) return '<span class="text-[9px] text-slate-600 uppercase">—</span>';
                            $d = \Carbon\Carbon::parse($date);
                            $daysLeft = now()->diffInDays($d, false);
                            if ($daysLeft < 0) return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider animate-pulse" style="background:rgba(255,71,87,0.15);color:#FF4757;border:1px solid rgba(255,71,87,0.25)">🔴 ' . $d->format('d M Y') . '</span>';
                            if ($daysLeft <= 7)  return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider" style="background:rgba(234,88,12,0.15);color:#FB923C;border:1px solid rgba(234,88,12,0.25)">⚠️ ' . $d->format('d M Y') . '</span>';
                            if ($daysLeft <= 30) return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider" style="background:rgba(245,158,11,0.15);color:#FBB03B;border:1px solid rgba(245,158,11,0.25)">🟡 ' . $d->format('d M Y') . '</span>';
                            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider" style="background:rgba(0,200,150,0.1);color:#00C896;border:1px solid rgba(0,200,150,0.2)">🟢 ' . $d->format('d M Y') . '</span>';
                        };
                        @endphp

                        <td class="py-4 px-6 text-center">{!! $docBadge($vehicle->registration_expiry) !!}</td>
                        <td class="py-4 px-6 text-center">{!! $docBadge($vehicle->insurance_expiry) !!}</td>
                        <td class="py-4 px-6 text-center">{!! $docBadge($vehicle->inspection_expiry_date) !!}</td>
                        <td class="py-4 px-6 text-center">{!! $docBadge($vehicle->route_permit_expiry) !!}</td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ url('/company/fleet/' . $vehicle->id . '/edit') }}"
                               class="inline-flex items-center gap-1 text-[9px] font-black text-cyan-400 uppercase hover:text-cyan-300 transition-colors tracking-widest">
                                Renew &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <div class="inline-flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background:rgba(0,200,150,0.1)">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-400">All documents are up to date 🎉</p>
                                <p class="text-[9px] text-slate-600 uppercase tracking-widest">No expiries within 30 days</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         ZONE 6: REVENUE VELOCITY CHART + FINANCIAL GRID
    ═══════════════════════════════════════════════════════ --}}
    <div class="section-header stagger-7">
        <div class="p-1.5 rounded-lg" style="background: rgba(0,200,150,0.1); border: 1px solid rgba(0,200,150,0.2);">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="text-base font-black text-white uppercase tracking-widest bebas">Financial Command Grid</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6 stagger-7">

        {{-- Revenue Velocity Chart --}}
        <div class="glass-card rounded-2xl p-6" x-data="{ revView: 'monthly' }">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Revenue Velocity</h3>
                    <p class="text-xl font-black text-white mt-0.5" x-text="revView === 'monthly' ? 'Monthly Trend' : (revView === 'daily' ? 'Daily Trend' : 'Weekly Trend')"></p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex p-1 rounded-lg gap-1" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.06);">
                        <button @click="revView = 'daily'; switchRevenueView('daily')"
                                :class="revView === 'daily' ? 'bg-slate-700 text-white shadow' : 'text-slate-500 hover:text-slate-300'"
                                class="px-3 py-1.5 text-[9px] font-black uppercase rounded-md transition-all">Daily</button>
                        <button @click="revView = 'monthly'; switchRevenueView('monthly')"
                                :class="revView === 'monthly' ? 'bg-slate-700 text-white shadow' : 'text-slate-500 hover:text-slate-300'"
                                class="px-3 py-1.5 text-[9px] font-black uppercase rounded-md transition-all">Monthly</button>
                    </div>
                </div>
            </div>
            <div class="h-60 relative">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        {{-- Financial Sub-Grid --}}
        <div class="space-y-5">
            {{-- AR Risk --}}
            <div class="glass-card rounded-2xl p-5 {{ $data['credit_blocked_count'] > 0 ? 'border-rose-500/20' : '' }}">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Cash Leakage Risk</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl" style="background: {{ $data['kpis']['overdue_rentals'] > 0 ? 'rgba(255,71,87,0.1)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $data['kpis']['overdue_rentals'] > 0 ? 'rgba(255,71,87,0.2)' : 'rgba(255,255,255,0.05)' }}">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold mb-1">Overdue Accounts</p>
                        <p class="text-2xl font-black {{ $data['kpis']['overdue_rentals'] > 0 ? 'text-red-400' : 'text-white' }}">{{ $data['kpis']['overdue_rentals'] }}</p>
                    </div>
                    <div class="p-4 rounded-xl" style="background: {{ $data['credit_blocked_count'] > 0 ? 'rgba(245,158,11,0.1)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $data['credit_blocked_count'] > 0 ? 'rgba(245,158,11,0.2)' : 'rgba(255,255,255,0.05)' }}">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold mb-1">Credit Blocked</p>
                        <p class="text-2xl font-black {{ $data['credit_blocked_count'] > 0 ? 'text-amber-400' : 'text-white' }}">{{ $data['credit_blocked_count'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Fine Recovery + VAT --}}
            <div class="grid grid-cols-2 gap-5">
                <div class="glass-card rounded-2xl p-5">
                    <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">Fine Recovery</h3>
                    <p class="text-xl font-black text-indigo-400">AED {{ number_format($data['fine_recovery']['recovered_this_month'], 0) }}</p>
                    <p class="text-[9px] font-bold mt-1 text-slate-500 uppercase flex items-center gap-1">
                        Rate: <span class="{{ $data['fine_recovery']['recovery_rate_pct'] < 80 ? 'text-rose-400' : 'text-emerald-400' }}">{{ $data['fine_recovery']['recovery_rate_pct'] }}%</span>
                    </p>
                </div>
                <div class="glass-card rounded-2xl p-5">
                    <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">VAT Payable</h3>
                    <p class="text-xl font-black text-white">AED {{ number_format($data['vat_summary']['net_vat_payable'], 0) }}</p>
                    <p class="text-[9px] font-bold mt-1 text-slate-500 uppercase">{{ $data['vat_summary']['quarter_label'] }}</p>
                </div>
            </div>

            {{-- Billing Engine --}}
            <div class="glass-card rounded-2xl p-5" style="border-color: rgba(6,182,212,0.15)">
                <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">Daily Billing Engine</h3>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-black text-cyan-400">AED {{ number_format($data['billing_today']['revenue_generated'] ?? 0, 0) }}</p>
                        <p class="text-[9px] text-slate-500 mt-1">{{ $data['billing_today']['contracts_billed'] ?? 0 }} contracts auto-billed today</p>
                    </div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:rgba(6,182,212,0.12)">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         ZONE 7: AI PREDICTIVE INTELLIGENCE
    ═══════════════════════════════════════════════════════ --}}
    <div class="section-header">
        <div class="p-1.5 rounded-lg" style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2);">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
        </div>
        <h2 class="text-base font-black text-white uppercase tracking-widest bebas">AI Predictive Intelligence</h2>
        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest text-indigo-300" style="background:rgba(99,102,241,0.2); border: 1px solid rgba(99,102,241,0.3)">AI Powered</span>
    </div>

    {{-- AI Risk Summary Strip --}}
    <div class="glass-card rounded-2xl p-6 mb-5 hover:border-white/10 transition-all group">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">AI Operational Risk Snapshot</h3>
                <p class="text-xl font-black text-white">System Stability Analysis</p>
            </div>
            <div class="flex items-center gap-8">
                {{-- Breakdown Risk Dial --}}
                <div class="flex items-center gap-3">
                    <div class="relative w-14 h-14">
                        <svg class="w-14 h-14 -rotate-90" viewBox="0 0 56 56">
                            <circle cx="28" cy="28" r="24" stroke-width="4" fill="transparent" class="stroke-current text-white/5"/>
                            <circle cx="28" cy="28" r="24" stroke-width="4" fill="transparent"
                                stroke-dasharray="150"
                                stroke-dashoffset="{{ 150 - (150 * ($data['kpis']['avg_risk_index'] ?? 5) / 100) }}"
                                class="stroke-current text-emerald-400 transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-black text-white">{{ $data['kpis']['avg_risk_index'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold">Breakdown Risk</p>
                        <p class="text-[10px] font-black text-emerald-400 flex items-center gap-1 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Low
                        </p>
                    </div>
                </div>
                <div class="w-px h-10 bg-white/5"></div>
                {{-- Driver Safety Dial --}}
                <div class="flex items-center gap-3">
                    <div class="relative w-14 h-14">
                        <svg class="w-14 h-14 -rotate-90" viewBox="0 0 56 56">
                            <circle cx="28" cy="28" r="24" stroke-width="4" fill="transparent" class="stroke-current text-white/5"/>
                            <circle cx="28" cy="28" r="24" stroke-width="4" fill="transparent"
                                stroke-dasharray="150"
                                stroke-dashoffset="{{ 150 - (150 * ($data['kpis']['avg_safe_score'] ?? 95) / 100) }}"
                                class="stroke-current text-emerald-400 transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-black text-white">{{ $data['kpis']['avg_safe_score'] ?? 100 }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold">Driver Safety</p>
                        <p class="text-[10px] font-black text-emerald-400 flex items-center gap-1 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Excellent
                        </p>
                    </div>
                </div>
                <div class="w-px h-10 bg-white/5"></div>
                <div class="text-right">
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold mb-1">Trend (6M)</p>
                    <span class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest text-slate-300" style="background:rgba(255,255,255,0.05)">Stable</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Breakdown vs Driver Risk Cards --}}
    @php
        $highRiskVehiclesCount = collect($data['predictive_maintenance']['predictions'])->where('risk_level', 'high')->count();
        $highRiskDriversCount = collect($data['driver_risk']['recent_snapshots'])->where('risk_level', 'high')->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

        {{-- Vehicle Breakdown Risk --}}
        <div class="relative rounded-2xl p-8 overflow-hidden group hover:border-rose-400/30 transition-all duration-500 flex flex-col"
             style="background: linear-gradient(135deg, #0A0F1E, #0F172A); border: 1px solid rgba(255,255,255,0.06); min-height: 240px;">
            <div class="absolute top-0 right-0 w-48 h-48 bg-rose-500/10 blur-3xl -mr-20 -mt-20 pointer-events-none transition-transform group-hover:scale-150 duration-700"></div>

            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-rose-400" style="background:rgba(255,71,87,0.12)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black text-rose-400 uppercase tracking-widest">Vehicle Breakdown</h3>
                        <p class="text-[9px] text-slate-600 uppercase mt-0.5">Probability Engine</p>
                    </div>
                </div>
                <span class="risk-high px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">Predictive</span>
            </div>

            <div class="flex flex-col items-center justify-center py-4 relative z-10 flex-1">
                <p class="text-7xl font-black tracking-tighter leading-none {{ $highRiskVehiclesCount > 0 ? 'text-red-500' : 'text-white' }}"
                   style="{{ $highRiskVehiclesCount > 0 ? 'filter: drop-shadow(0 0 20px rgba(255,71,87,0.4))' : '' }}">
                    {{ $highRiskVehiclesCount }}
                </p>
                <p class="text-[9px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Units at Critical Risk</p>
            </div>

            <div class="relative z-10 pt-5 border-t border-white/5 flex items-center justify-between mt-auto">
                @if($highRiskVehiclesCount > 0)
                <p class="text-xs font-black text-red-400 flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span></span>
                    High Probability Detected
                </p>
                <a href="{{ route('company.fleet.index') }}" class="text-[9px] font-black text-slate-400 hover:text-red-400 uppercase tracking-widest transition-colors">View Fleet &rarr;</a>
                @else
                <p class="text-xs font-black text-emerald-400 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 live-dot"></span> No High Risk Units
                </p>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">All Clear</span>
                @endif
            </div>
        </div>

        {{-- Driver Accident Risk --}}
        <div class="relative rounded-2xl p-8 overflow-hidden group hover:border-amber-400/30 transition-all duration-500 flex flex-col"
             style="background: linear-gradient(135deg, #0A0F1E, #0F172A); border: 1px solid rgba(255,255,255,0.06); min-height: 240px;">
            <div class="absolute top-0 right-0 w-48 h-48 bg-amber-500/10 blur-3xl -mr-20 -mt-20 pointer-events-none transition-transform group-hover:scale-150 duration-700"></div>

            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-amber-400" style="background:rgba(245,158,11,0.12)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black text-amber-400 uppercase tracking-widest">Driver Accident Risk</h3>
                        <p class="text-[9px] text-slate-600 uppercase mt-0.5">Behavioral Model</p>
                    </div>
                </div>
                <span class="risk-medium px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">Active</span>
            </div>

            <div class="flex flex-col items-center justify-center py-4 relative z-10 flex-1">
                <p class="text-7xl font-black tracking-tighter leading-none {{ $highRiskDriversCount > 0 ? 'text-amber-500' : 'text-white' }}"
                   style="{{ $highRiskDriversCount > 0 ? 'filter: drop-shadow(0 0 20px rgba(245,158,11,0.4))' : '' }}">
                    {{ $highRiskDriversCount }}
                </p>
                <p class="text-[9px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Drivers at Risk</p>
            </div>

            <div class="relative z-10 pt-5 border-t border-white/5 flex items-center justify-between mt-auto">
                @if($highRiskDriversCount > 0)
                <p class="text-xs font-black text-amber-400 flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span></span>
                    Immediate Coaching Needed
                </p>
                <button class="text-[9px] font-black text-slate-400 hover:text-amber-400 uppercase tracking-widest transition-colors">Action List &rarr;</button>
                @else
                <p class="text-xs font-black text-emerald-400 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 live-dot"></span> No High Risk Drivers
                </p>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">All Clear</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Driver Risk Monitor Table --}}
    <div class="glass-card rounded-2xl p-6 mb-5">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Driver Risk Monitor</h3>
                <p class="text-xs text-slate-600 mt-1">Safety scoring (0-100) based on speeding, harsh driving, fines, and compliance</p>
            </div>
            <div class="flex items-center space-x-6">
                <div class="text-center">
                    <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">High Risk</p>
                    <p class="text-lg font-black text-rose-500">{{ $data['driver_risk']['high_risk_count'] }}</p>
                </div>
                <div class="text-center border-l border-white/5 pl-6">
                    <p class="text-[9px] font-black text-amber-400 uppercase tracking-widest">Medium Risk</p>
                    <p class="text-lg font-black text-amber-500">{{ $data['driver_risk']['medium_risk_count'] }}</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto ops-detail transition-all duration-500">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04)">
                        <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-left">Driver</th>
                        <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-left">Safety Score</th>
                        <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-left">Risk Level</th>
                        <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-left">Violations (30d)</th>
                        <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['driver_risk']['recent_snapshots'] as $snapshot)
                    @php $breakdown = json_decode($snapshot->breakdown_json, true); @endphp
                    <tr class="hover:bg-white/[0.02] transition-colors border-b border-white/[0.03]">
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-black text-slate-300"
                                     style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08)">
                                    {{ substr($snapshot->first_name, 0, 1) }}{{ substr($snapshot->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-200">{{ $snapshot->first_name }} {{ $snapshot->last_name }}</p>
                                    <p class="text-[9px] text-slate-600">ID: #{{ $snapshot->driver_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 w-20 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.06)">
                                    <div class="h-full {{ $snapshot->score >= 80 ? 'bg-emerald-500' : ($snapshot->score >= 60 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $snapshot->score }}%"></div>
                                </div>
                                <span class="text-xs font-black text-white">{{ $snapshot->score }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-2">
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest
                                {{ $snapshot->risk_level === 'low' ? 'risk-low' : ($snapshot->risk_level === 'medium' ? 'risk-medium' : 'risk-high') }}">
                                {{ $snapshot->risk_level }} risk
                            </span>
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-3 text-[9px] text-slate-500">
                                <span>⚡ {{ $breakdown['speed'] < 100 ? 'Alerts' : 'Clean' }}</span>
                                <span>🚗 {{ $breakdown['harsh'] < 100 ? 'Harsh' : 'Safe' }}</span>
                                <span>📋 {{ $breakdown['compliance'] }}%</span>
                            </div>
                        </td>
                        <td class="py-4 px-2 text-right">
                            <a href="{{ url('/portal/drivers/' . $snapshot->driver_id) }}" class="text-[9px] font-black text-cyan-400 uppercase hover:text-cyan-300 transition-colors">Review &rarr;</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-sm text-slate-600">No risk profiling data available. System will calculate nightly.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Fleet Replacement Intelligence --}}
    <div class="glass-card rounded-2xl p-6 mb-5">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">🚍 Fleet Replacement Intelligence</h3>
                <p class="text-xs text-slate-600 mt-1">Lifecycle analysis based on margin decline, maintenance escalation, and reliability</p>
            </div>
            <div class="flex items-center space-x-10">
                <div class="text-center">
                    <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Replace Soon</p>
                    <p class="text-xl font-black text-rose-500">{{ $data['fleet_replacement']['replace_count'] }}</p>
                </div>
                <div class="text-center border-l border-white/5 pl-8">
                    <p class="text-[9px] font-black text-amber-400 uppercase tracking-widest">Under Review</p>
                    <p class="text-xl font-black text-amber-500">{{ $data['fleet_replacement']['monitor_count'] }}</p>
                </div>
                <div class="text-center border-l border-white/5 pl-8">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Fleet Avg Score</p>
                    <p class="text-xl font-black text-white">{{ $data['fleet_replacement']['avg_fleet_score'] }}</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04)">
                            <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-left">Vehicle</th>
                            <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-left">Age</th>
                            <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-left">R-Score</th>
                            <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-left">Recommendation</th>
                            <th class="pb-3 px-2 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['fleet_replacement']['top_replacement_candidates'] as $candidate)
                        @php $age = $candidate->purchase_date ? \Carbon\Carbon::parse($candidate->purchase_date)->diffInYears(now()) : 'N/A'; @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors border-b border-white/[0.03]">
                            <td class="py-4 px-2">
                                <div class="flex items-center gap-3">
                                    <span class="text-base">🚌</span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-200">{{ $candidate->name }}</p>
                                        <p class="text-[9px] text-slate-600">{{ $candidate->vehicle_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-2 text-xs font-medium text-slate-400">{{ $age }} Yrs</td>
                            <td class="py-4 px-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black {{ $candidate->replacement_score >= 80 ? 'text-rose-400' : ($candidate->replacement_score >= 60 ? 'text-amber-400' : 'text-emerald-400') }}">{{ $candidate->replacement_score }}</span>
                                    <div class="h-1 w-12 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.06)">
                                        <div class="h-full {{ $candidate->replacement_score >= 80 ? 'bg-rose-500' : ($candidate->replacement_score >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width:{{ $candidate->replacement_score }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-2">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest
                                    {{ $candidate->recommendation === 'replace' ? 'risk-high' : ($candidate->recommendation === 'monitor' ? 'risk-medium' : 'risk-low') }}">
                                    {{ $candidate->recommendation }}
                                </span>
                            </td>
                            <td class="py-4 px-2 text-right">
                                <a href="{{ url('/portal/vehicles/' . $candidate->vehicle_id) }}" class="text-[9px] font-black text-cyan-400 uppercase hover:text-cyan-300 transition-colors">Analysis &rarr;</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm text-slate-600">Fleet intelligence processing. Results update weekly.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="rounded-xl p-5" style="background:rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05)">
                <div class="flex items-center gap-2 mb-4">
                    <span>💰</span>
                    <h4 class="text-xs font-black text-white uppercase tracking-widest">Capital Planning</h4>
                </div>
                <div class="space-y-4">
                    <div>
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1">Expected Margin Increase</p>
                        <p class="text-2xl font-black text-emerald-400">+{{ $data['fleet_replacement']['margin_increase_pct'] }}%</p>
                    </div>
                    <div class="pt-4" style="border-top: 1px solid rgba(255,255,255,0.05)">
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1">Projected OpEx Savings</p>
                        <p class="text-2xl font-black text-cyan-400">{{ number_format($data['fleet_replacement']['projected_savings']) }} AED</p>
                    </div>
                </div>
                <button class="w-full py-3 mt-6 text-[9px] font-black uppercase tracking-widest text-white rounded-xl transition-all hover:opacity-90"
                        style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1)">
                    Download CapEx Report
                </button>
            </div>
        </div>
    </div>

    {{-- AI Dynamic Pricing --}}
    <div class="relative rounded-2xl p-6 mb-5 overflow-hidden" style="background: #0A0F1E; border: 1px solid rgba(6,182,212,0.12)">
        <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/5 blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest text-cyan-400"
                          style="background:rgba(6,182,212,0.12); border: 1px solid rgba(6,182,212,0.2)">AI Dynamic Pricing</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                </div>
                <h3 class="text-lg font-black text-white uppercase bebas tracking-wide">💰 Revenue Optimization Panel</h3>
                <p class="text-[10px] text-slate-500 mt-1">Real-time yield management based on utilization, seasonality, and urgency signals</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-12">
                <div>
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1">Revenue Lift</p>
                    <p class="text-2xl font-black text-emerald-400 leading-none">+{{ $data['pricing_optimization']['revenue_lift_pct'] }}%</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1">AI Generated</p>
                    <p class="text-2xl font-black text-white leading-none">AED {{ number_format($data['pricing_optimization']['total_ai_revenue'], 0) }}</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1">Optimized Avg</p>
                    <p class="text-2xl font-black text-cyan-400 leading-none">AED {{ number_format($data['pricing_optimization']['avg_optimized_rate'], 0) }}</p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1">Market Volume</p>
                    <p class="text-2xl font-black text-slate-500 leading-none">{{ $data['pricing_optimization']['decisions_count'] }} <span class="text-[10px]">Qs</span></p>
                </div>
            </div>
        </div>
        @if($data['pricing_optimization']['has_data'])
        <div class="mt-8 pt-6" style="border-top: 1px solid rgba(255,255,255,0.04)">
            <h4 class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Recent Automated Decisions</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($data['pricing_optimization']['recent_decisions'] as $decision)
                <div class="p-3 rounded-xl" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.05)">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[9px] font-black text-slate-600 uppercase">Opt. Rate</span>
                        <span class="text-[10px] font-black text-emerald-400">AED {{ number_format($decision->optimized_rate, 0) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        @php $m = is_string($decision->multipliers_json) ? json_decode($decision->multipliers_json, true) : (array)$decision->multipliers_json; @endphp
                        @if(($m['utilization_multiplier'] ?? 1) > 1) <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> @endif
                        @if(($m['season_multiplier'] ?? 1) > 1) <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> @endif
                        @if(($m['urgency_multiplier'] ?? 1) > 1) <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> @endif
                        <span class="text-[9px] text-slate-600 font-bold uppercase truncate">Veh #{{ $decision->vehicle_id }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════
         ZONE 8: FLEET SNAPSHOT + UTILIZATION TREND
    ═══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="space-y-4">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.04)">Fleet Snapshot</h3>
            <div class="grid grid-cols-2 gap-4">
                @php
                    $utilPct = number_format($data['efficiency']['idle_pct'] ?? 0, 0) == 0 ? 100 : 100 - number_format($data['efficiency']['idle_pct'], 0);
                @endphp
                <div class="glass-card rounded-xl p-4">
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1.5">Utilization</p>
                    <p class="text-xl font-black text-white">{{ $utilPct }}%</p>
                </div>
                <div class="glass-card rounded-xl p-4">
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1.5">Rev / KM</p>
                    <p class="text-xl font-black text-white">{{ isset($data['efficiency']['revenue_per_km']) ? number_format($data['efficiency']['revenue_per_km'], 1) : '0' }}</p>
                </div>
                <div class="glass-card rounded-xl p-4">
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1.5">Maint / KM</p>
                    <p class="text-xl font-black text-white">{{ isset($data['efficiency']['maintenance_cost_per_km']) ? number_format($data['efficiency']['maintenance_cost_per_km'], 1) : '0' }}</p>
                </div>
                <div class="glass-card rounded-xl p-4">
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1.5">Idle Units</p>
                    <p class="text-xl font-black {{ ($data['efficiency']['idle_count'] ?? 0) > 0 ? 'text-amber-400' : 'text-white' }}">{{ $data['efficiency']['idle_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="lg:col-span-2 flex flex-col">
            <div class="flex items-center justify-between mb-3 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.04)">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Fleet Utilization Trend</h3>
                <span class="text-[9px] font-black text-cyan-400 uppercase tracking-widest">Last 30 Days</span>
            </div>
            <div class="glass-card rounded-2xl p-5 flex-1" style="min-height: 180px;" data-chart-container="utilization"></div>
        </div>
    </div>

    {{-- Branch Benchmarks --}}
    @if($data['branch_benchmarks']['has_data'] ?? false)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="glass-card rounded-2xl p-4 flex items-center gap-4 hover:border-emerald-500/20 transition-all">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shrink-0" style="background:rgba(0,200,150,0.1)">🏆</div>
            <div>
                <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold mb-1">Best Branch</p>
                <p class="text-base font-black text-white">{{ $data['branch_benchmarks']['best_branch']['name'] ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-4 flex items-center gap-4 hover:border-rose-500/20 transition-all">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shrink-0" style="background:rgba(255,71,87,0.1)">⚠️</div>
            <div>
                <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold mb-1">Highest Risk</p>
                <p class="text-base font-black text-white">{{ $data['branch_benchmarks']['worst_branch']['name'] ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-4 flex items-center gap-4 hover:border-cyan-500/20 transition-all">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shrink-0" style="background:rgba(6,182,212,0.1)">💰</div>
            <div>
                <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold mb-1">Highest Margin</p>
                <p class="text-base font-black text-white">{{ $data['branch_benchmarks']['highest_margin']['name'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif

    @include('company.dashboard.partials.quick-actions')

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Map loading hide after components mount
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const overlay = document.getElementById('map-loading');
            if (overlay) {
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.6s ease';
                setTimeout(() => overlay.remove(), 600);
            }
        }, 2000);

        // ── Chart.js Global Defaults ──────────────────────────────────
        Chart.defaults.color = '#64748b';
        Chart.defaults.font.family = "'DM Sans', sans-serif";

        // ── Revenue Trend Chart ───────────────────────────────────────
        const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
        const trendGrad = trendCtx.createLinearGradient(0, 0, 0, 280);
        trendGrad.addColorStop(0, 'rgba(0,200,150,0.3)');
        trendGrad.addColorStop(1, 'rgba(0,200,150,0.0)');

        const expGrad = trendCtx.createLinearGradient(0, 0, 0, 280);
        expGrad.addColorStop(0, 'rgba(255,71,87,0.2)');
        expGrad.addColorStop(1, 'rgba(255,71,87,0.0)');

        const revenueData = {
            monthly: <?= json_encode($data['charts']['revenue_trend_monthly']) ?>,
            daily:   <?= json_encode($data['charts']['revenue_trend_daily']) ?>
        };

        const revenueChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: revenueData.monthly.labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData.monthly.revenue,
                    borderColor: '#00C896',
                    borderWidth: 2.5,
                    backgroundColor: trendGrad,
                    fill: true, tension: 0.4,
                    pointBackgroundColor: '#0A0F1E',
                    pointBorderColor: '#00C896',
                    pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6
                }, {
                    label: 'Expenses',
                    data: revenueData.monthly.expenses,
                    borderColor: '#FF4757',
                    borderWidth: 2,
                    backgroundColor: expGrad,
                    fill: true, tension: 0.4,
                    borderDash: [5, 3],
                    pointBackgroundColor: '#0A0F1E',
                    pointBorderColor: '#FF4757',
                    pointBorderWidth: 2, pointRadius: 3, pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top', align: 'end', labels: { boxWidth: 14, boxHeight: 2, usePointStyle: true, pointStyle: 'line', font: { size: 10, weight: 'bold' } } },
                    tooltip: {
                        backgroundColor: '#0F172A', borderColor: 'rgba(255,255,255,0.06)', borderWidth: 1,
                        padding: 12, cornerRadius: 10, displayColors: true,
                        callbacks: { label: ctx => ctx.dataset.label + ': AED ' + ctx.parsed.y.toLocaleString() }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.04)' },
                        ticks: { font: { size: 10 }, callback: v => 'AED ' + v.toLocaleString() }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });

        window.switchRevenueView = function(view) {
            const d = revenueData[view];
            revenueChart.data.labels = d.labels;
            revenueChart.data.datasets[0].data = d.revenue;
            revenueChart.data.datasets[1].data = d.expenses;
            revenueChart.update('active');
        };

        // ── Utilization Trend Chart ────────────────────────────────────
        const utilContainer = document.querySelector('[data-chart-container="utilization"]');
        if (utilContainer) {
            const utilCanvas = document.createElement('canvas');
            utilContainer.appendChild(utilCanvas);
            const utilCtx = utilCanvas.getContext('2d');

            const utilGrad = utilCtx.createLinearGradient(0, 0, 0, 180);
            utilGrad.addColorStop(0, 'rgba(0,200,150,0.25)');
            utilGrad.addColorStop(1, 'rgba(0,200,150,0.0)');

            new Chart(utilCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($data['charts']['utilization_trend']['labels']) ?>,
                    datasets: [{
                        label: 'Active Vehicles',
                        data: <?= json_encode($data['charts']['utilization_trend']['values']) ?>,
                        borderColor: '#00C896', borderWidth: 2.5,
                        backgroundColor: utilGrad, fill: true, tension: 0.4,
                        pointRadius: 0, pointHoverRadius: 5,
                        pointBackgroundColor: '#00C896'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { font: { size: 10 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });
        }

        // ── Predictive Risk Trend Chart ────────────────────────────────
        const riskTrendEl = document.getElementById('riskTrendChart');
        if (riskTrendEl) {
            const riskTrendCtx = riskTrendEl.getContext('2d');
            new Chart(riskTrendCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($data['predictive_risk']['trends']['labels']) ?>,
                    datasets: [{
                        label: 'Breakdown Risk',
                        data: <?= json_encode($data['predictive_risk']['trends']['vehicle_data']) ?>,
                        borderColor: '#FF4757', backgroundColor: 'rgba(255,71,87,0.08)',
                        borderWidth: 2.5, tension: 0.4, fill: true,
                        pointRadius: 3, pointBackgroundColor: '#FF4757'
                    }, {
                        label: 'Accident Risk',
                        data: <?= json_encode($data['predictive_risk']['trends']['driver_data']) ?>,
                        borderColor: '#F59E0B', backgroundColor: 'rgba(245,158,11,0.08)',
                        borderWidth: 2.5, tension: 0.4, fill: true,
                        pointRadius: 3, pointBackgroundColor: '#F59E0B'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A', borderColor: 'rgba(255,255,255,0.06)', borderWidth: 1,
                            mode: 'index', intersect: false, padding: 12, cornerRadius: 10,
                            callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y}%` }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { font: { size: 10 }, callback: v => v + '%' } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });
        }

        // ── Branch Charts ──────────────────────────────────────────────
        <?php if ($data['branch_benchmarks']['has_data']): ?>
        const perfCtxEl = document.getElementById('branchPerformanceChart');
        if (perfCtxEl) {
            new Chart(perfCtxEl.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($data['branch_benchmarks']['chart_data']['labels']) ?>,
                    datasets: [
                        { label: 'Revenue', data: <?= json_encode($data['branch_benchmarks']['chart_data']['revenue']) ?>, backgroundColor: 'rgba(6,182,212,0.7)', borderRadius: 5 },
                        { label: 'Margin', data: <?= json_encode($data['branch_benchmarks']['chart_data']['margin']) ?>, backgroundColor: 'rgba(0,200,150,0.7)', borderRadius: 5 },
                        { label: 'Utilization', data: <?= json_encode($data['branch_benchmarks']['chart_data']['utilization']) ?>, backgroundColor: 'rgba(245,158,11,0.7)', borderRadius: 5 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } } },
                    scales: {
                        y: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { font: { size: 10 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });
        }
        const growthCtxEl = document.getElementById('branchGrowthChart');
        if (growthCtxEl) {
            new Chart(growthCtxEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($data['branch_benchmarks']['growth_trends']['labels']) ?>,
                    datasets: [
                        <?php foreach ($data['branch_benchmarks']['growth_trends']['series'] as $index => $series): ?>{
                            label: '<?= $series["name"] ?>',
                            data: <?= json_encode($series["data"]) ?>,
                            borderColor: ['#06b6d4','#00C896','#f59e0b','#6366f1','#ec4899'][<?= $index ?> % 5],
                            borderWidth: 2, tension: 0.4, fill: false, pointRadius: 3
                        },<?php endforeach; ?>
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { font: { size: 10 }, callback: v => 'AED ' + v.toLocaleString() } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });
        }
        <?php endif; ?>

        // ── View Toggle ────────────────────────────────────────────────
        const btnExec = document.getElementById('btn-exec-view');
        const btnOps  = document.getElementById('btn-ops-view');
        const opsDetails  = document.querySelectorAll('.ops-detail');
        const mapContainer = document.getElementById('zone3-map-container');

        function setView(view) {
            if (view === 'executive') {
                btnExec.className = 'px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg bg-slate-700 text-white shadow-sm transition-all';
                btnOps.className  = 'px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg text-slate-400 hover:text-white transition-all';
                opsDetails.forEach(el => { el.style.opacity = '0'; setTimeout(() => el.classList.add('hidden'), 250); });
                if (mapContainer) { mapContainer.classList.remove('lg:col-span-2'); mapContainer.classList.add('lg:col-span-3'); }
            } else {
                btnOps.className  = 'px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg bg-slate-700 text-white shadow-sm transition-all';
                btnExec.className = 'px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg text-slate-400 hover:text-white transition-all';
                opsDetails.forEach(el => { el.classList.remove('hidden'); setTimeout(() => el.style.opacity = '1', 10); });
                if (mapContainer) { mapContainer.classList.remove('lg:col-span-3'); mapContainer.classList.add('lg:col-span-2'); }
            }
        }

        if (btnExec) btnExec.addEventListener('click', () => setView('executive'));
        if (btnOps)  btnOps.addEventListener('click', () => setView('operations'));
        setView('executive');
    });

    // ── Map Fullscreen Toggle ────────────────────────────────────────
    function toggleMapFullscreen() {
        const map = document.getElementById('zone3-map-container');
        if (!map) return;
        if (map.style.position === 'fixed') {
            Object.assign(map.style, { position:'', inset:'', zIndex:'', borderRadius:'' });
        } else {
            Object.assign(map.style, { position:'fixed', inset:'0', zIndex:'9999', borderRadius:'0' });
        }
        setTimeout(() => window.dispatchEvent(new Event('resize')), 100);
    }
</script>
@endpush