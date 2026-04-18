<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#0B1120] text-slate-300">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SaaS Control Panel') | Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/images.png') }}">

    <!-- Tailwind CSS (via CDN for simplicity, replace with Vite in prod) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    @stack('styles')
</head>

<body class="h-full antialiased font-sans text-slate-300 bg-[#0B1120]">
    <div x-data="{ sidebarOpen: false }" class="min-h-full flex">

        <!-- Sidebar Navigation -->
        @include('layouts.partials.super-admin-sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 lg:ml-64">

            <!-- Top Navigation -->
            <header class="bg-[#0B1120] border-b border-slate-800/60 sticky top-0 z-30 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="flex-1 lg:flex-none">
                    @yield('header_title', '<h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest">SaaS Control Panel</h1>')
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-slate-300">{{ auth()->user()->name ?? 'Super Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-cyan-500 hover:text-cyan-400 font-medium">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                <div class="mb-4 bg-emerald-900/30 border border-emerald-500/30 text-emerald-400 p-4 rounded-lg flex items-center">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="mb-4 bg-rose-900/30 border border-rose-500/30 text-rose-400 p-4 rounded-lg flex items-center">
                    {{ session('error') }}
                </div>
                @endif

                <!-- System Broadcasts Injection (Admin Preview) -->
                @php
                $globalBroadcasts = \App\Models\AdminBroadcast::where('is_active', true)
                ->where(function($query) {
                $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
                })
                ->latest()
                ->get();
                @endphp

                @if($globalBroadcasts->isNotEmpty() && !request()->routeIs('admin.broadcasts.*'))
                <div class="space-y-3 mb-6">
                    @foreach($globalBroadcasts as $alert)
                    <div class="bg-amber-500/10 border border-amber-500/30 rounded-lg p-4 flex items-start shadow-sm relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
                        <svg class="h-6 w-6 text-amber-500 mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <h4 class="text-sm font-bold text-slate-100 uppercase tracking-wider">Active System Broadcast</h4>
                                <div class="text-[10px] font-mono text-slate-400">Live &nbsp;|&nbsp; Target: {{ strtoupper($alert->target_role) }}</div>
                            </div>
                            <p class="text-sm text-slate-300 font-medium leading-relaxed">{{ $alert->message }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>