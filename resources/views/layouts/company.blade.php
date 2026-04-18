<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode, 'h-full bg-gray-50 dark:bg-slate-950': true }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ session('api_token') }}">

    <title>@yield('title', 'Fleet Control') - {{ auth()->user()->company->name ?? 'Company' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/images.png') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            // Ensure company layout specific tailwind config is isolated here if needed
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <style>
        @media print {
            .lg\:ml-64 {
                margin-left: 0 !important;
            }

            header,
            aside,
            .d-print-none,
            .toast-container,
            [x-data] button {
                display: none !important;
            }

            main {
                padding: 0 !important;
                background: white !important;
            }

            .print\:shadow-none {
                box-shadow: none !important;
            }

            .print\:border-none {
                border: none !important;
            }

            .print\:p-0 {
                padding: 0 !important;
            }

            .print\:m-0 {
                margin: 0 !important;
            }

            body {
                background: white !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="h-full antialiased font-sans text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-slate-950">
    <div x-data="{ sidebarOpen: false }" class="min-h-full flex">

        <!-- Sidebar Navigation -->
        @include('layouts.partials.company-sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 lg:ml-64">

            @if(session()->has('impersonator_id'))
            <div class="bg-rose-600 px-4 py-3 text-white flex justify-between items-center sm:px-6 lg:px-8 border-b border-rose-700 shadow-md relative z-50">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span class="font-bold tracking-wider text-sm uppercase">Super Admin Impersonation Mode</span>
                </div>
                <form method="POST" action="{{ route('impersonation.leave') }}">
                    @csrf
                    <button type="submit" class="bg-white/20 hover:bg-white/30 text-white px-4 py-1.5 rounded-lg text-sm font-bold shadow-sm transition-all focus:ring-2 focus:ring-white">
                        Return to Super Admin
                    </button>
                </form>
            </div>
            @endif

            <!-- Top Navigation -->
            <header class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800 sticky top-0 z-30 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-100">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="flex-1 lg:flex-none">
                    @yield('header_title')
                </div>

                <!-- Right side profile menu -->
                <div class="flex items-center space-x-4">
                    <button @click="darkMode = !darkMode" class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition-colors rounded-lg">
                        <svg x-show="darkMode" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 4.22a1 1 0 011.415 0l.708.707a1 1 0 01-1.414 1.414l-.708-.707a1 1 0 010-1.414zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-4.22 4.22a1 1 0 010 1.415l-.707.708a1 1 0 01-1.414-1.414l.707-.708a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-4.22-4.22a1 1 0 01-1.415 0l-.708-.707a1 1 0 011.414-1.414l.708.707a1 1 0 010 1.414zM4 10a1 1 0 01-1 1H2a1 1 0 110-2h1a1 1 0 011 1zm4.22-4.22a1 1 0 010-1.415l.707-.708a1 1 0 011.414 1.414l-.707.708a1 1 0 01-1.414 0zM10 5a5 5 0 100 10 5 5 0 000-10z"></path>
                        </svg>
                        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>

                    <!-- Notification Bell -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition-colors rounded-lg relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if(isset($systemNotifications) && $systemNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-900"></span>
                            @endif
                        </button>

                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 z-50 overflow-hidden"
                            style="display: none;">
                            <div class="p-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">Notifications</h3>
                                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $systemNotifications->count() ?? 0 }} New</span>
                            </div>
                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                @forelse($systemNotifications ?? [] as $notification)
                                <a href="{{ $notification['link'] }}" class="block p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-gray-50 dark:border-slate-800 last:border-0 text-left">
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center 
                                            {{ $notification['severity'] === 'warning' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                            {{ $notification['severity'] === 'error' ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400' : '' }}
                                            {{ $notification['severity'] === 'info' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        ">
                                            @if($notification['category'] === 'Vehicle') 🚛 @elseif($notification['category'] === 'Driver') 👤 @elseif($notification['category'] === 'Finance') 💰 @else 📋 @endif
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $notification['title'] }}</p>
                                            <p class="text-[10px] text-slate-500 mt-0.5 leading-relaxed">{{ $notification['message'] }}</p>
                                            <p class="text-[9px] text-blue-500 font-bold uppercase mt-1 tracking-wider">{{ $notification['category'] }}</p>
                                        </div>
                                    </div>
                                </a>
                                @empty
                                <div class="p-8 text-center text-left">
                                    <p class="text-xs text-slate-500">No new notifications</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold truncate max-w-[150px]">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 uppercase">{{ auth()->user()->role === 'company_admin' ? 'Owner' : 'Staff' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <!-- Floating Toast Notifications -->
                <div x-data="{
                        toasts: [],
                        add(msg, type = 'success') {
                            const id = Date.now();
                            this.toasts.push({ id, msg, type });
                            setTimeout(() => this.remove(id), 5000); // 5 sec dismiss
                        },
                        remove(id) {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }
                    }"
                    x-init="
                        @if(session('success')) add('{{ addslashes(session('success')) }}', 'success'); @endif
                        @if(session('error')) add('{{ addslashes(session('error')) }}', 'error'); @endif
                    "
                    class="fixed bottom-6 right-6 z-100 flex flex-col gap-3 pointer-events-none w-full max-w-sm">

                    <template x-for="toast in toasts" :key="toast.id">
                        <div x-show="true"
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="translate-y-10 opacity-0 relative"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            :class="toast.type === 'success' ? 'bg-emerald-900 border-emerald-500/30 shadow-[0_4px_20px_-4px_rgba(16,185,129,0.3)]' : 'bg-rose-900 border-rose-500/30 shadow-[0_4px_20px_-4px_rgba(225,29,72,0.3)]'"
                            class="pointer-events-auto rounded-2xl border p-4 flex items-start gap-4 ring-1 ring-white/10">

                            <!-- Icon (Success) -->
                            <div x-show="toast.type === 'success'" class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <!-- Icon (Error) -->
                            <div x-show="toast.type === 'error'" class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0 pt-0.5">
                                <h3 class="text-xs font-black uppercase tracking-widest text-white mb-1" x-text="toast.type === 'success' ? 'Success' : 'Attention Needed'"></h3>
                                <p class="text-sm font-medium text-slate-300 leading-snug" x-text="toast.msg"></p>
                            </div>

                            <button @click="remove(toast.id)" class="text-slate-400 hover:text-white transition-colors shrink-0 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- System Broadcasts Injection (Tenant View) -->
                @php
                $tenantCompanyId = (int) session('company_id');
                $activeBroadcasts = \App\Models\AdminBroadcast::where('is_active', true)
                ->where(function($query) use ($tenantCompanyId) {
                $query->whereNull('company_id')
                ->orWhere('company_id', $tenantCompanyId);
                })
                ->where(function($query) {
                $query->where('target_role', 'all')
                ->orWhere('target_role', 'company_admin'); // Assuming company layout is used by admins/managers mostly
                })
                ->where(function($query) {
                $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
                })
                ->latest()
                ->get();
                @endphp

                @if($activeBroadcasts->isNotEmpty())
                <div class="space-y-3 mb-6">
                    @foreach($activeBroadcasts as $alert)
                    <div class="bg-amber-500/10 border border-amber-500/30 rounded-lg p-4 flex items-start shadow-sm relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
                        <svg class="h-6 w-6 text-amber-500 mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1 uppercase tracking-wider">System Announcement</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400 font-medium leading-relaxed">{{ $alert->message }}</p>
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