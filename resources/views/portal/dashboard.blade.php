@extends('layouts.company')

@section('title', 'Fleet Control Center — OwnBus')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-main: #070B14;
        --bg-card: #0D1117;
        --border-card: rgba(255,255,255,0.06);
        --border-card-hover: rgba(255,255,255,0.12);
        
        --c-teal: #00D4AA;
        --c-gold: #FFB800;
        --c-red: #FF4757;
        --c-purple: #A855F7;
        --c-blue: #3B82F6;
        --c-orange: #F97316;
        --c-emerald: #10B981;
        
        --text-primary: #F9FAFB;
        --text-secondary: #9CA3AF;
        --radius: 16px;
    }
    body { 
        font-family: 'DM Sans', sans-serif; 
        background-color: var(--bg-main); 
        color: var(--text-primary);
    }
    h1, h2, h3, .heading-font { font-family: 'Inter', sans-serif; }

    /* ── BASE CARD ── */
    .premium-card {
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: var(--radius);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 24px rgba(0,0,0,0.4), inset 0 0 0 1px rgba(255,255,255,0.02);
        transition: all 0.3s ease;
    }
    .premium-card:hover {
        border-color: var(--border-card-hover);
        transform: translateY(-2px);
    }

    /* ── METRIC CARD ── */
    .metric-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
        border: 1px solid var(--border-card);
        border-radius: var(--radius);
        padding: 20px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        min-width: 160px;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        border-color: var(--border-card-hover);
    }
    .metric-card.m-teal { border-left: 3px solid var(--c-teal); }
    .metric-card.m-gold { border-left: 3px solid var(--c-gold); }
    .metric-card.m-blue { border-left: 3px solid var(--c-blue); }
    .metric-card.m-purple { border-left: 3px solid var(--c-purple); }
    .metric-card.m-orange { border-left: 3px solid var(--c-orange); }
    .metric-card.m-red { border-left: 3px solid var(--c-red); }
    .metric-card.m-emerald { border-left: 3px solid var(--c-emerald); }

    /* ── QUICK ACTIONS ── */
    .qa-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 16px 12px;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none;
        color: var(--text-primary);
        position: relative;
        overflow: hidden;
    }
    .qa-card::after {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 100%);
        opacity: 0; transition: opacity 0.3s;
    }
    .qa-card:active::after { opacity: 1; }
    .qa-card:hover {
        background: rgba(255,255,255,0.06);
        border-color: var(--c-teal);
        transform: translateY(-2px);
    }
    .qa-icon { font-size: 28px; }
    .qa-text { font-size: 10px; font-weight: 700; uppercase; letter-spacing: 0.05em; line-height: 1.2; }

    /* ── ANIMATIONS ── */
    @keyframes count-up {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-value { animation: count-up 0.6s ease forwards; }

    @keyframes fade-slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stagger-1 { animation: fade-slide-up 0.45s ease 0.05s both; }
    .stagger-2 { animation: fade-slide-up 0.45s ease 0.12s both; }
    .stagger-3 { animation: fade-slide-up 0.45s ease 0.20s both; }
    .stagger-4 { animation: fade-slide-up 0.45s ease 0.28s both; }
    .stagger-5 { animation: fade-slide-up 0.45s ease 0.36s both; }

    @keyframes live-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
        50%      { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
    }
    .live-dot { animation: live-pulse 1.8s infinite; }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    .blink-anim { animation: blink 1.5s infinite; }

    @keyframes gauge-fill { from { stroke-dashoffset: 502; } }
    .gauge-animated { animation: gauge-fill 1.5s ease-out 0.3s both; }

    /* ── CUSTOM SCROLLBARS ── */
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    
    .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

    /* ── HEADER TABS ── */
    .tab-btn {
        padding: 8px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;
        color: var(--text-secondary); border-bottom: 2px solid transparent; transition: all 0.3s ease;
    }
    .tab-btn.active { color: var(--text-primary); border-bottom-color: var(--c-teal); }
    .tab-btn:hover:not(.active) { color: var(--text-primary); border-bottom-color: rgba(0, 212, 170, 0.3); }

    /* ── SYSTEM STABLE ── */
    .system-status-card {
        background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(6,78,59,0.4));
        position: relative;
        overflow: hidden;
    }
    .system-status-card::before {
        content: ''; position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0.5; pointer-events: none;
    }

    /* ── DOC TABLE ── */
    .premium-table th { background: rgba(255,255,255,0.02); color: var(--text-secondary); font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; border-bottom: 1px solid var(--border-card); font-weight: 600; }
    .premium-table td { padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.02); font-size: 13px; }
    .premium-table tr:hover td { background: rgba(255,255,255,0.01); }
    .premium-table tr:last-child td { border-bottom: none; }
</style>
@endpush

@section('content')
<div x-data="{
        activeTab: 'executive',
        now: new Date()
     }" class="space-y-6 max-w-7xl mx-auto pb-10 pt-4">

    {{-- SECTION 1 - TOP HEADER BAR --}}
    <div class="premium-card px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 stagger-1 relative overflow-hidden" style="border-top: 1px solid rgba(255,255,255,0.1);">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 live-dot"></div>
            <div>
                <h1 class="heading-font text-lg font-bold tracking-widest uppercase text-white">Fleet Operations Control</h1>
                <p class="text-xs text-slate-400 font-medium tracking-wide mt-0.5">{{ $company->name ?? 'Company Name' }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-6 relative z-10">
            <div class="text-right hidden md:block">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ now()->format('D d M') }}</p>
                <p class="text-sm font-black text-white tracking-widest" id="live-clock">{{ now()->format('H:i') }} <span class="text-[10px] text-slate-500">GST</span></p>
            </div>
            
            <div class="flex items-center gap-2 border-l border-white/10 pl-6">
                <button @click="activeTab = 'executive'" :class="activeTab == 'executive' ? 'active' : ''" class="tab-btn">Executive</button>
                <button @click="activeTab = 'ops'" :class="activeTab == 'ops' ? 'active' : ''" class="tab-btn">Ops</button>
            </div>
        </div>
    </div>

    {{-- SECTION 2 - HERO METRICS ROW --}}
    @php
        $riskScore = $data['risk_score']['score'] ?? 0;
        $riskLabel = $riskScore < 50 ? 'LOW' : ($riskScore < 75 ? 'MEDIUM' : 'HIGH');
        $riskColorClass = $riskScore < 50 ? 'm-emerald' : ($riskScore < 75 ? 'm-gold' : 'm-red');
        $riskTextColor = $riskScore < 50 ? 'text-emerald-400' : ($riskScore < 75 ? 'text-amber-400' : 'text-rose-400');
        
        $maintCount = $data['kpis']['vehicles_in_maintenance'] ?? 0;
        $outAmt = $data['kpis']['outstanding_payments'] ?? 0;
        $util = $data['charts']['fleet_utilization'] ?? 0;
    @endphp

    <div class="overflow-x-auto hide-scroll stagger-2 pb-2 -mx-4 px-4 md:mx-0 md:px-0">
        <div class="flex md:grid md:grid-cols-4 xl:grid-cols-7 gap-4 w-max md:w-auto">
            
            {{-- Fleet Status --}}
            <div class="metric-card m-teal">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="text-cyan-400">🚌</span> Fleet Status</p>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black text-white stat-value">{{ $data['kpis']['active_rentals'] }}</span>
                    <span class="text-xs text-slate-500 font-bold">/ {{ $data['kpis']['total_vehicles'] }}</span>
                </div>
                <div class="mt-3 w-full h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-cyan-400" style="width: {{ $data['kpis']['total_vehicles'] > 0 ? ($data['kpis']['active_rentals'] / $data['kpis']['total_vehicles']) * 100 : 0 }}%"></div>
                </div>
                <p class="text-[9px] font-bold text-cyan-400 mt-2 uppercase tracking-wider flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span> Active Buses</p>
            </div>

            {{-- MTD Revenue --}}
            <div class="metric-card m-gold">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="text-amber-400">💰</span> MTD Revenue</p>
                <p class="text-xl font-black text-amber-400 stat-value">AED {{ number_format($data['kpis']['revenue_this_month'], 0) }}</p>
                <p class="text-[10px] text-emerald-400 font-bold mt-2 uppercase tracking-wider flex items-center gap-1">↑ Trending Up ▲</p>
            </div>

            {{-- Utilization --}}
            <div class="metric-card m-blue">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="text-blue-400">📊</span> Utilization</p>
                <p class="text-xl font-black {{ $util < 0 ? 'text-rose-400' : 'text-blue-400' }} stat-value">{{ $util }}%</p>
                <div class="text-[9px] text-slate-500 font-mono mt-1 mb-1 tracking-widest">[<span class="text-blue-400">||||||||</span>··]</div>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Deployed</p>
            </div>

            {{-- Risk Index --}}
            <div class="metric-card {{ $riskColorClass }}">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="{{ $riskTextColor }}">⚠️</span> Risk Index</p>
                <p class="text-xl font-black {{ $riskTextColor }} stat-value">{{ $riskLabel }}</p>
                <p class="text-[10px] text-slate-300 font-bold mt-1">Score: {{ $riskScore }}</p>
                <p class="text-[9px] font-bold {{ $riskTextColor }} mt-1 uppercase tracking-wider flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full {{ $riskTextColor === 'text-emerald-400' ? 'bg-emerald-400' : 'bg-rose-400 blink-anim' }}"></span> All Clear</p>
            </div>

            {{-- VAT Payable --}}
            <div class="metric-card m-purple">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="text-purple-400">🧾</span> VAT Payable</p>
                <p class="text-lg font-black text-purple-400 stat-value">AED {{ number_format($data['vat_summary']['net_vat_payable'] ?? 0, 0) }}</p>
                <p class="text-[10px] text-slate-300 font-bold mt-1">{{ $data['vat_summary']['quarter_label'] ?? 'Q2 2026' }}</p>
                <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-wider">Due Date</p>
            </div>

            {{-- Maintenance --}}
            <div class="metric-card m-orange">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="text-orange-400">🔧</span> Maintenance</p>
                <p class="text-2xl font-black {{ $maintCount > 0 ? 'text-orange-400' : 'text-white' }} stat-value">{{ $maintCount }}</p>
                <p class="text-[10px] text-slate-300 font-bold mt-1 uppercase">Buses</p>
                @if($maintCount == 0)
                <p class="text-[9px] font-bold text-emerald-400 mt-1 uppercase tracking-wider flex items-center gap-1">✓ All Clear</p>
                @else
                <p class="text-[9px] font-bold text-orange-400 mt-1 uppercase tracking-wider flex items-center gap-1">⚠ In Shop</p>
                @endif
            </div>

            {{-- Outstanding --}}
            <div class="metric-card m-red">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="text-rose-400">💳</span> Outstanding</p>
                <p class="text-lg font-black {{ $outAmt > 0 ? 'text-rose-400' : 'text-white' }} stat-value">AED {{ number_format($outAmt, 0) }}</p>
                @if($outAmt == 0)
                <p class="text-[10px] text-emerald-400 font-bold mt-1 uppercase tracking-wider">Collected ✓</p>
                <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-wider">All Clear</p>
                @else
                <p class="text-[10px] text-rose-400 font-bold mt-1 uppercase tracking-wider blink-anim">Due</p>
                @endif
            </div>

        </div>
    </div>

    {{-- SECTION 3 - QUICK ACTIONS --}}
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 stagger-3">
        <a href="{{ route('company.fleet.create') }}" class="qa-card">
            <span class="qa-icon text-cyan-400">🚌</span>
            <span class="qa-text">Add<br>Vehicle</span>
        </a>
        <a href="{{ route('company.rentals.create') }}" class="qa-card">
            <span class="qa-icon text-blue-400">📋</span>
            <span class="qa-text">Create<br>Rental</span>
        </a>
        <a href="{{ route('company.customers.create') }}" class="qa-card">
            <span class="qa-icon text-purple-400">👤</span>
            <span class="qa-text">Add<br>Customer</span>
        </a>
        <a href="{{ route('company.finance.invoices') }}" class="qa-card">
            <span class="qa-icon text-gold-400">💳</span>
            <span class="qa-text">Record<br>Payment</span>
        </a>
        <a href="{{ route('company.kanban.index') }}" class="qa-card">
            <span class="qa-icon text-orange-400">👨‍✈️</span>
            <span class="qa-text">Assign<br>Driver</span>
        </a>
        <a href="{{ route('company.fines.create') }}" class="qa-card">
            <span class="qa-icon text-rose-400">🚦</span>
            <span class="qa-text">Record<br>Fine</span>
        </a>
    </div>

    {{-- SECTION 4 - MAIN CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 stagger-4">
        
        {{-- LEFT COLUMN (65%) --}}
        <div class="lg:col-span-2 space-y-6 flex flex-col">
            
            {{-- System Status Card --}}
            @php $hasHighRisk = collect($data['risks'])->where('severity', 'high')->count() > 0; @endphp
            <div class="premium-card system-status-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 shrink-0 {{ $hasHighRisk ? 'border-rose-500/50' : 'border-emerald-500/30' }}" style="{{ $hasHighRisk ? 'background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(127,29,29,0.4));' : '' }}">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-3 h-3 rounded-full {{ $hasHighRisk ? 'bg-rose-500 blink-anim' : 'bg-emerald-500 live-dot' }}"></span>
                        <h2 class="heading-font text-xl font-black text-white uppercase tracking-widest">{{ $hasHighRisk ? 'CRITICAL RISK DETECTED' : 'SYSTEM STABLE' }}</h2>
                    </div>
                    @if($hasHighRisk)
                    <p class="text-sm text-rose-200 font-medium mb-4">Immediate Action Required. Operations at risk.</p>
                    @else
                    <p class="text-sm text-emerald-100 font-medium mb-4">No Critical Risks. Operations running efficiently.</p>
                    @endif
                    <div class="flex items-center gap-4 text-[10px] font-bold text-white uppercase tracking-widest font-mono">
                        <span class="px-2 py-1 bg-black/40 rounded">████ DB ✅</span>
                        <span class="px-2 py-1 bg-black/40 rounded">████ GPS ✅</span>
                        <span class="px-2 py-1 bg-black/40 rounded">████ Q ✅</span>
                    </div>
                </div>
                <div class="relative z-10 shrink-0">
                    <button onclick="document.getElementById('riskCenterModal')?.showModal()" class="px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-white transition-all bg-black/30 hover:bg-black/50 border border-white/10 flex items-center gap-2">
                        View Center <span>→</span>
                    </button>
                </div>
            </div>

            {{-- GPS Map Card --}}
            <div class="premium-card flex flex-col flex-1 min-h-[400px] overflow-hidden">
                <div class="p-4 border-b border-white/5 flex items-center justify-between shrink-0 bg-white/[0.02]">
                    <div>
                        <h3 class="heading-font text-sm font-black text-cyan-400 uppercase tracking-widest">GPS Intelligence</h3>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">Live Fleet Map</p>
                    </div>
                    <button class="px-3 py-1.5 rounded bg-white/5 hover:bg-white/10 text-[9px] font-black uppercase tracking-widest text-slate-300 transition-colors flex items-center gap-1.5">
                        Expand ⤢
                    </button>
                </div>
                
                <div class="px-4 py-2 bg-[#060D1A] flex items-center gap-3 border-b border-white/5 shrink-0 overflow-x-auto hide-scroll">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 blink-anim"></span>
                        <span class="text-[9px] font-bold text-cyan-400 uppercase tracking-widest">Connecting...</span>
                    </div>
                    <div class="w-px h-3 bg-white/10"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Live</span>
                    </div>
                    <div class="w-px h-3 bg-white/10"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Off</span>
                    </div>
                    <div class="w-px h-3 bg-white/10"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">No GPS</span>
                    </div>
                </div>

                <div class="relative flex-1 bg-[#060D1A]">
                    <x-dashboard.fleet-map :companyId="$company->id" />
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN (35%) --}}
        <div class="space-y-6 flex flex-col">
            
            {{-- Company Health Card --}}
            @php
                $eff = $data['efficiency']['score'] ?? 80;
                $healthScore = max(0, min(100, round(($eff + (100 - $riskScore)) / 2)));
                $hCircum = 502;
                $hOffset = $hCircum - ($hCircum * $healthScore / 100);
            @endphp
            <div class="premium-card p-6 flex flex-col items-center relative overflow-hidden">
                <h3 class="heading-font text-[11px] font-black text-slate-400 uppercase tracking-widest mb-6 w-full text-center">Company Health Matrix</h3>
                
                <div class="relative w-32 h-32 mb-6">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 192 192">
                        <circle cx="96" cy="96" r="80" stroke-width="12" fill="transparent" stroke="rgba(255,255,255,0.05)"/>
                        <circle cx="96" cy="96" r="80" stroke-width="12" fill="transparent" stroke-linecap="round"
                            stroke="var(--c-gold)"
                            stroke-dasharray="{{ $hCircum }}"
                            stroke-dashoffset="{{ $hOffset }}"
                            class="gauge-animated"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-black text-white leading-none heading-font">{{ $healthScore }}</span>
                        <span class="text-[10px] font-black text-slate-500 uppercase mt-1">/ 100</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 mb-6">
                    <span class="w-2 h-2 rounded-full bg-amber-500 blink-anim"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-400">Monitor</span>
                </div>

                <div class="w-full grid grid-cols-2 gap-y-4 gap-x-2 text-[10px] font-bold text-slate-300 uppercase tracking-wider text-center">
                    <div><span class="text-slate-500 block text-[9px] mb-1">Maint</span>0%</div>
                    <div><span class="text-slate-500 block text-[9px] mb-1">Docs</span>0%</div>
                    <div><span class="text-slate-500 block text-[9px] mb-1">AR</span>0%</div>
                    <div><span class="text-slate-500 block text-[9px] mb-1">Fines</span>0%</div>
                </div>
            </div>

            {{-- Subscription Card --}}
            @php $isExpired = $company->subscription_status !== 'active'; @endphp
            <div class="premium-card p-6 relative overflow-hidden {{ $isExpired ? 'border-rose-500/50 shadow-[0_0_20px_rgba(244,63,94,0.15)]' : 'border-emerald-500/30' }}">
                <h3 class="heading-font text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 mb-4">
                    📅 Subscription
                </h3>

                <div class="flex items-center gap-3 mb-4">
                    <span class="text-xs font-black text-white uppercase tracking-widest">{{ $company->subscription_label }}</span>
                    <span class="flex items-center gap-1.5 px-2 py-0.5 rounded {{ $isExpired ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400' }} text-[9px] font-black uppercase tracking-widest">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isExpired ? 'bg-rose-500 blink-anim' : 'bg-emerald-400' }}"></span>
                        {{ $isExpired ? 'Expired' : 'Active' }}
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-xl font-black text-white heading-font">{{ $company->days_remaining }} <span class="text-xs text-slate-500 font-medium uppercase tracking-widest">Days Left</span></p>
                    <div class="text-[9px] text-slate-500 font-mono mt-2 tracking-widest w-full">
                        [<span class="{{ $isExpired ? 'text-rose-500' : 'text-emerald-500' }}">{{ str_repeat('░', 20) }}</span>] 100%
                    </div>
                </div>

                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-6">
                    Expires: <span class="text-white">{{ $company->trial_ends_at ? $company->trial_ends_at->format('d M Y') : 'N/A' }}</span>
                </p>

                @if($isExpired)
                <a href="{{ route('subscription.upgrade') }}" class="block w-full py-3 bg-rose-600 hover:bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest text-center rounded-xl transition-all shadow-lg border border-rose-400/50">
                    ⚡ Upgrade Now →
                </a>
                @endif
            </div>

            {{-- Event Stream --}}
            <div class="premium-card p-6 flex flex-col flex-1 min-h-[250px]">
                <div class="flex items-center justify-between mb-5 shrink-0 border-b border-white/5 pb-3">
                    <h3 class="heading-font text-[11px] font-black text-slate-400 uppercase tracking-widest">Live Event Stream</h3>
                    <a href="#" class="text-[9px] font-black text-cyan-400 hover:text-cyan-300 uppercase tracking-widest transition-colors">All →</a>
                </div>
                
                <div class="overflow-y-auto flex-1 custom-scroll space-y-1">
                    @forelse(collect($data['timeline'])->take(6) as $event)
                    @php
                        $aL = strtolower($event->action);
                        $dotColor = str_contains($aL, 'payment') ? 'bg-amber-400' : (str_contains($aL, 'fine') ? 'bg-rose-400' : (str_contains($aL, 'rental') ? 'bg-emerald-400' : 'bg-cyan-400'));
                    @endphp
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 transition-colors group cursor-pointer">
                        <span class="w-2 h-2 rounded-full {{ $dotColor }} shrink-0 group-hover:scale-125 transition-transform"></span>
                        <p class="text-[10px] text-slate-300 truncate flex-1 font-medium">{{ $event->action }}</p>
                        <p class="text-[9px] text-slate-600 uppercase font-bold shrink-0 tracking-widest">{{ \Carbon\Carbon::parse($event->occurred_at)->diffForHumans(null, true, true) }}</p>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <p class="text-[9px] font-bold text-slate-600 uppercase tracking-widest">No recent events</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- SECTION 5 & 6 GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 stagger-5">
        
        {{-- SECTION 5 - DOCUMENT EXPIRY TRACKER (65%) --}}
        <div class="lg:col-span-2 premium-card flex flex-col overflow-hidden min-h-[300px]">
            <div class="p-5 border-b border-white/5 flex items-center justify-between shrink-0 bg-white/[0.02]">
                <h3 class="heading-font text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    📄 Document Expiry Tracker
                </h3>
                <a href="{{ route('company.fleet.create') }}" class="px-3 py-1.5 rounded bg-white/5 hover:bg-white/10 text-[9px] font-black uppercase tracking-widest text-slate-300 transition-colors flex items-center gap-1.5">
                    + Add
                </a>
            </div>
            <div class="overflow-x-auto flex-1 custom-scroll">
                <table class="w-full text-left premium-table">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Registration</th>
                            <th>Insurance</th>
                            <th>Inspection</th>
                            <th>Route Permit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(collect($data['expiring_vehicles'])->take(5) as $vehicle)
                        @php
                            $fmtDate = function($d) {
                                if(!$d) return '<span class="text-[10px] font-bold text-slate-600 uppercase">—</span>';
                                $dt = \Carbon\Carbon::parse($d);
                                $left = now()->diffInDays($dt, false);
                                if($left < 0) return '<span class="text-[10px] font-black text-rose-400 uppercase flex items-center gap-1">❌ Expired</span>';
                                if($left <= 30) return '<span class="text-[10px] font-black text-orange-400 uppercase flex items-center gap-1">⚠️ Soon</span>';
                                return '<span class="text-[10px] font-black text-emerald-400 uppercase flex items-center gap-1">✅ Valid</span>';
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="text-lg">🚌</span>
                                    <div>
                                        <p class="text-xs font-bold text-white">{{ $vehicle->name }}</p>
                                        <p class="text-[9px] text-slate-500 uppercase font-medium">{{ $vehicle->vehicle_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{!! $fmtDate($vehicle->registration_expiry) !!}</td>
                            <td>{!! $fmtDate($vehicle->insurance_expiry) !!}</td>
                            <td>{!! $fmtDate($vehicle->inspection_expiry_date) !!}</td>
                            <td>{!! $fmtDate($vehicle->route_permit_expiry) !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center opacity-50">
                                    <span class="text-4xl mb-3">📄</span>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">All documents valid</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SECTION 6 - ACTIVE JOBS (35%) --}}
        <div class="premium-card flex flex-col overflow-hidden min-h-[300px]">
            <div class="p-5 border-b border-white/5 flex items-center justify-between shrink-0 bg-white/[0.02]">
                <h3 class="heading-font text-[11px] font-black text-slate-400 uppercase tracking-widest">Active Jobs</h3>
                <a href="{{ route('company.rentals.index') }}" class="text-[9px] font-black text-cyan-400 hover:text-cyan-300 uppercase tracking-widest transition-colors">View All</a>
            </div>
            <div class="overflow-y-auto flex-1 p-3 custom-scroll space-y-1">
                @forelse(collect($data['active_rentals'])->take(5) as $rental)
                <div class="p-3 rounded-xl border border-transparent hover:border-white/5 hover:bg-white/5 transition-all group cursor-pointer flex items-center justify-between">
                    <div class="flex items-start gap-3">
                        <span class="text-cyan-400 text-lg group-hover:scale-110 transition-transform">🚌</span>
                        <div>
                            <p class="text-[11px] font-bold text-white mb-0.5">{{ $rental->vehicle->name ?? 'Vehicle' }}</p>
                            <p class="text-[9px] text-slate-400 font-medium">{{ $rental->customer->name ?? 'Customer' }} — {{ $rental->start_date ? \Carbon\Carbon::parse($rental->start_date)->format('d M') : 'N/A' }}</p>
                        </div>
                    </div>
                    @if($rental->status === 'active')
                    <span class="w-2 h-2 rounded-full bg-emerald-500 live-dot shrink-0"></span>
                    @else
                    <span class="w-2 h-2 rounded-full bg-slate-500 shrink-0"></span>
                    @endif
                </div>
                @empty
                <div class="flex flex-col items-center justify-center h-full opacity-50 pt-10">
                    <span class="text-3xl mb-3">📭</span>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">No Active Jobs</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Live Clock Update
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('live-clock').innerHTML = `${hrs}:${mins} <span class="text-[10px] text-slate-500">GST</span>`;
    }
    setInterval(updateClock, 1000);
</script>
@endpush
