@php
$currentRoute = request()->route()->getName();

$links = [
// 1. OVERVIEW
['is_header' => true, 'name' => 'Overview'],
['name' => 'Dashboard', 'route' => 'company.dashboard', 'pattern' => 'company.dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
['name' => 'Executive Intel', 'route' => 'company.intelligence.executive', 'pattern' => 'company.intelligence.*', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],

// 2. OPERATIONS MANAGEMENT
['is_header' => true, 'name' => 'Operations'],
['name' => 'Fleet Management', 'route' => '#', 'pattern' => ['company.fleet.*', 'company.maintenance.*', 'company.expenses.*', 'company.fuel.*', 'company.trips.*', 'company.breakdowns.*'], 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'children' => [
    ['name' => 'Vehicles', 'route' => 'company.fleet.index'],
    ['name' => 'Trips', 'route' => 'company.trips.index'],
    ['name' => 'Fuel Logs', 'route' => 'company.fuel.index'],
    ['name' => 'Maintenance', 'route' => 'company.maintenance.index'],
    ['name' => 'Predictive Health', 'route' => 'company.maintenance.predictions'],
    ['name' => 'GPS Tracking', 'route' => 'company.telematics.dashboard'],
    ['name' => 'War Room Map', 'route' => 'company.command-center'],
]],
['name' => 'Driver & Staff', 'route' => '#', 'pattern' => ['company.drivers.*', 'company.staff.*'], 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'children' => [
    ['name' => 'Drivers', 'route' => 'company.drivers.index'],
    ['name' => 'Kanban Board', 'route' => 'company.kanban.index'],
]],

// 3. RENTALS & REVENUE
['is_header' => true, 'name' => 'Rentals & Revenue'],
['name' => 'All Rentals', 'route' => 'company.rentals.index', 'pattern' => 'company.rentals.*', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
['name' => 'Live Pricing Engine', 'route' => 'company.pricing.index', 'pattern' => 'company.pricing.*', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
['name' => 'Customers', 'route' => 'company.customers.index', 'pattern' => 'company.customers.*', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],

// 4. FINANCE & ACCOUNTING
['is_header' => true, 'name' => 'Finance & Accounting'],
['name' => 'Accounting Core', 'route' => '#', 'pattern' => ['company.accounting.*'], 'icon' => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2', 'children' => [
    ['name' => 'Accounting Dashboard', 'route' => 'company.accounting.index'],
    ['name' => 'Chart of Accounts', 'route' => 'company.accounting.coa'],
    ['name' => 'Journal Entries', 'route' => 'company.accounting.journals'],
]],

// 5. ADMINISTRATION
['is_header' => true, 'name' => 'Administration'],
['name' => 'SaaS Subscription', 'route' => 'subscription.show', 'pattern' => 'subscription.*', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
['name' => 'System Control', 'route' => '#', 'pattern' => ['company.settings.*'], 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'children' => [
    ['name' => 'Users', 'route' => 'company.settings.index'],
    ['name' => 'Audit Logs', 'route' => 'company.activity.index'],
]],
];
@endphp

<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" @click="sidebarOpen = false"></div>

<!-- Sidebar -->
<aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 border-r border-gray-100 dark:border-slate-800 transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col shadow-sm">

    <!-- Logo area -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100 dark:border-slate-800">
        <a href="{{ route('company.dashboard') }}" class="flex items-center space-x-3">
            @php $company = auth()->user()->company; @endphp
            @if($company && ($company->logo_url || $company->logo_path))
            <img src="{{ $company->logo_url ?? Storage::url($company->logo_path) }}" alt="Logo" class="h-8 w-8 object-contain rounded bg-white">
            @else
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-black text-xl leading-none">B</span>
            </div>
            @endif
            <span class="text-lg font-bold tracking-tight text-gray-900 dark:text-white truncate max-w-[140px]">{{ auth()->user()->company->name ?? 'Fleet Control' }}</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-900 dark:hover:text-white">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- User Profile Quick View -->
    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-800 flex items-center justify-center">
            <span class="text-blue-700 dark:text-blue-400 font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest">{{ auth()->user()->role === 'company_admin' ? 'Owner' : 'Staff' }}</p>
        </div>
    </div>

    <!-- Navigation List -->
    <div class="flex-1 overflow-y-auto py-6 px-4">
        <nav class="space-y-1">
            <p class="px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Menu</p>

            @foreach($links as $link)
            @if(isset($link['is_header']))
            <p class="px-3 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 mt-4">{{ $link['name'] }}</p>
            @else
            @php
            $patterns = is_array($link['pattern'] ?? []) ? $link['pattern'] : [$link['pattern'] ?? '###'];
            $isActive = request()->routeIs(...$patterns);
            $hasChildren = isset($link['children']);
            @endphp

            <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
                <a href="{{ $hasChildren ? '#' : ($link['route'] !== '#' ? route($link['route']) : '#') }}"
                    @if($hasChildren) @click.prevent="open = !open" @endif
                    class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 
                  {{ $isActive 
                       ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' 
                       : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors mr-3
                        {{ $isActive 
                            ? 'bg-blue-600 text-white shadow shadow-blue-500/30' 
                            : 'bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-500 group-hover:bg-white dark:group-hover:bg-slate-700 group-hover:text-gray-600 dark:group-hover:text-gray-300 shadow-sm' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{!! $link['icon'] !!}"></path>
                        </svg>
                    </div>
                    <span class="font-semibold tracking-tight flex-1">{{ $link['name'] }}</span>
                    @if($hasChildren)
                    <svg :class="open ? 'rotate-90' : ''" class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    @endif
                </a>

                @if($hasChildren)
                <div x-show="open" x-collapse x-cloak class="mt-1 ml-11 space-y-1">
                    @foreach($link['children'] as $child)
                    @php
                    $isChildActive = request()->routeIs($child['route']);
                    @endphp
                    <a href="{{ $child['route'] === '#' ? '#' : route($child['route']) }}"
                        class="block px-3 py-2 text-xs font-semibold rounded-lg transition-colors
                                {{ $isChildActive 
                                    ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' 
                                    : 'text-gray-500 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                        {{ $child['name'] }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
            @endforeach
        </nav>
    </div>
</aside>