@php
$currentRoute = request()->route()->getName();

$links = [
['name' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
['name' => 'Admin Requests', 'route' => 'admin.requests.index', 'pattern' => 'admin.requests.*', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
['name' => 'Companies', 'route' => 'admin.companies.index', 'pattern' => 'admin.companies.*', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
['name' => 'Subscriptions / Plans', 'route' => 'admin.plans.index', 'pattern' => 'admin.plans.*', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
['name' => 'Billing & Revenue', 'route' => 'admin.billing.index', 'pattern' => 'admin.billing.*', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
['name' => 'Usage Analytics', 'route' => 'admin.analytics.index', 'pattern' => 'admin.analytics.*', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
['name' => 'Support / Tickets', 'route' => 'admin.support.index', 'pattern' => 'admin.support.*', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
['name' => 'System Monitoring', 'route' => 'admin.system.index', 'pattern' => 'admin.system.*', 'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z'],
['name' => 'Broadcast / Comm', 'route' => 'admin.broadcasts.index', 'pattern' => 'admin.broadcasts.*', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
['name' => 'Agreement / ToS', 'route' => 'admin.agreements.index', 'pattern' => 'admin.agreements.*', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
['name' => 'Audit Logs', 'route' => 'admin.audit-logs.index', 'pattern' => 'admin.audit-logs.*', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
['name' => 'Settings', 'route' => 'admin.settings.index', 'pattern' => 'admin.settings.*', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
];
@endphp

<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" @click="sidebarOpen = false"></div>

<!-- Sidebar -->
<aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0B1120] border-r border-slate-800 text-slate-300 transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col shadow-2xl">

    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 bg-[#0B1120] border-b border-slate-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
            <svg class="w-8 h-8 text-cyan-500 drop-shadow-[0_0_8px_rgba(6,182,212,0.8)]" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5H5.5L12 6.5z" />
            </svg>
            <span class="text-sm font-black tracking-widest text-white uppercase group-hover:text-cyan-400 transition-colors">Aetheired<br /><span class="text-xs text-slate-400 font-medium tracking-normal">Systems</span></span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation List -->
    <div class="flex-1 overflow-y-auto py-6 px-4">
        <nav class="space-y-1">
            @foreach($links as $link)
            @php
            $isActive = request()->routeIs($link['pattern']);
            @endphp
            <a href="{{ $link['route'] !== '#' ? route($link['route']) : '#' }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 
                  {{ $isActive ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/50 shadow-[0_0_15px_rgba(6,182,212,0.15)]' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }}">
                <svg class="h-5 w-5 mr-3 {{ $isActive ? 'text-cyan-400 drop-shadow-[0_0_5px_rgba(6,182,212,0.8)]' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path class="fill-current opacity-20" d="{!! explode(' ', $link['icon'])[0] !!}"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{!! $link['icon'] !!}"></path>
                </svg>
                {{ $link['name'] }}
            </a>
            @endforeach
        </nav>
    </div>
</aside>