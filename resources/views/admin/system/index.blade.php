@extends('layouts.super-admin')

@section('title', 'System Monitoring | SaaS Admin')

@section('header_title')
<div class="flex items-center space-x-4">
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)] flex items-center">
        <svg class="h-6 w-6 mr-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
        </svg>
        System Diagnostics & Health
    </h1>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Global Environment Header -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <!-- Laravel Engine -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-4 shadow-lg flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Laravel Engine</p>
                <h3 class="text-lg font-bold text-slate-200">v{{ $systemInfo['environment']['laravel_version'] }}</h3>
            </div>
        </div>

        <!-- PHP Runtime -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-4 shadow-lg flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20 shrink-0">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PHP Runtime</p>
                <h3 class="text-lg font-bold text-slate-200">v{{ $systemInfo['environment']['php_version'] }}</h3>
            </div>
        </div>

        <!-- Application Environment -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-4 shadow-lg flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg {{ $systemInfo['environment']['app_env'] === 'production' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-amber-500/10 text-amber-500 border-amber-500/20' }} flex items-center justify-center border shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">App Env</p>
                <h3 class="text-lg font-bold text-slate-200 capitalize">{{ $systemInfo['environment']['app_env'] }}</h3>
            </div>
        </div>

        <!-- Debug Mode -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-4 shadow-lg flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg {{ $systemInfo['environment']['debug_mode'] === 'Enabled' ? 'bg-rose-500/10 text-rose-500 border-rose-500/20 animate-pulse' : 'bg-slate-800 text-slate-500 border-slate-700' }} flex items-center justify-center border shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Debug State</p>
                <h3 class="text-lg font-bold {{ $systemInfo['environment']['debug_mode'] === 'Enabled' ? 'text-rose-500' : 'text-slate-400' }}">{{ $systemInfo['environment']['debug_mode'] }}</h3>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Physical Server Architecture -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Operating System & Database -->
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg p-6">
                <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-widest mb-6 flex items-center">
                    <svg class="h-4 w-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                    Hardware & Data Engine
                </h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Server Architecture</p>
                        <p class="text-sm font-mono text-slate-300 bg-slate-900/50 p-3 rounded-lg border border-slate-800">{{ $systemInfo['environment']['os'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Database Engine</p>
                        <p class="text-sm font-mono text-slate-300 bg-slate-900/50 p-3 rounded-lg border border-slate-800">
                            {{ $systemInfo['database']['driver'] }} ({{ $systemInfo['database']['database_name'] }})
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex justify-between items-end mb-2">
                        <p class="text-xs text-slate-500 uppercase tracking-widest">Database Size (Approximate)</p>
                        <p class="text-sm font-bold text-cyan-400 font-mono">{{ $systemInfo['database']['size_mb'] }} MB</p>
                    </div>
                    <!-- Fake progress bar representing DB size against a hypothetical 1GB quota -->
                    <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-linear-to-r from-cyan-600 to-cyan-400 h-1.5 rounded-full" style="width: {{ min(($systemInfo['database']['size_mb'] / 1024) * 100, 100) }}%;"></div>
                    </div>
                </div>
            </div>

            <!-- Server Storage Diagnostics -->
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg p-6">
                <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-widest mb-6 flex items-center">
                    <svg class="h-4 w-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Disk Constraints (Storage Path)
                </h3>

                @if($systemInfo['storage']['available'])
                <div class="flex justify-between text-xs text-slate-400 mb-2 font-mono">
                    <span>Used: <span class="text-amber-500 font-bold">{{ $systemInfo['storage']['used_gb'] }} GB</span></span>
                    <span>Free: <span class="text-emerald-400 font-bold">{{ $systemInfo['storage']['free_gb'] }} GB</span></span>
                    <span>Total: <span class="text-slate-300 font-bold">{{ $systemInfo['storage']['total_gb'] }} GB</span></span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden shadow-inner border border-slate-700">
                    <div class="bg-linear-to-r {{ $systemInfo['storage']['usage_percent'] > 90 ? 'from-rose-600 to-rose-400' : 'from-indigo-600 to-cyan-400' }} h-full rounded-full transition-all duration-1000" style="width: {{ $systemInfo['storage']['usage_percent'] }}%;"></div>
                </div>
                <p class="text-right text-[10px] text-slate-500 mt-2 font-bold">{{ $systemInfo['storage']['usage_percent'] }}% CONSUMED</p>
                @else
                <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-lg text-rose-400 text-xs font-mono">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    {{ $systemInfo['storage']['message'] }}
                </div>
                @endif
            </div>

        </div>

        <!-- Right Configuration Panel -->
        <div class="space-y-6 lg:col-span-1">

            <!-- Core Limits -->
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/50">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">PHP Configuration Maps</h3>
                </div>
                <div class="divide-y divide-slate-800/60 p-2">
                    <div class="px-4 py-3 flex justify-between items-center group hover:bg-slate-800/30 rounded transition-colors">
                        <span class="text-sm border-b border-dashed border-slate-600 text-slate-300 font-mono">memory_limit</span>
                        <span class="text-sm font-bold text-cyan-400">{{ $systemInfo['php_config']['memory_limit'] }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-center group hover:bg-slate-800/30 rounded transition-colors">
                        <span class="text-sm border-b border-dashed border-slate-600 text-slate-300 font-mono">max_execution_time</span>
                        <span class="text-sm font-bold text-emerald-400">{{ $systemInfo['php_config']['max_execution_time'] }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-center group hover:bg-slate-800/30 rounded transition-colors">
                        <span class="text-sm border-b border-dashed border-slate-600 text-slate-300 font-mono">upload_max_filesize</span>
                        <span class="text-sm font-bold text-amber-500">{{ $systemInfo['php_config']['upload_max_filesize'] }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-center group hover:bg-slate-800/30 rounded transition-colors">
                        <span class="text-sm border-b border-dashed border-slate-600 text-slate-300 font-mono">post_max_size</span>
                        <span class="text-sm font-bold text-amber-500">{{ $systemInfo['php_config']['post_max_size'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Extensions Safety Check -->
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/50">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Required Extensions</h3>
                </div>
                <div class="grid grid-cols-2 p-4 gap-3">
                    @foreach($systemInfo['php_config']['extensions'] as $ext => $loaded)
                    <div class="flex items-center space-x-2">
                        @if($loaded)
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        @else
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        @endif
                        <span class="text-xs font-mono {{ $loaded ? 'text-slate-300' : 'text-slate-500 line-through' }} uppercase">{{ $ext }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Asynchronous Queues -->
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg p-6 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 text-slate-800/30 group-hover:text-cyan-500/5 transition-colors duration-1000">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"></path>
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-widest mb-6">Background Workers</h3>

                    <div class="flex justify-between items-center mb-4 border-b border-slate-800/80 pb-4">
                        <div class="flex items-center text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 mr-2 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-widest">Pending Sync</span>
                        </div>
                        <span class="text-xl font-black text-cyan-400">{{ $systemInfo['queues']['pending_jobs'] }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex items-center text-slate-500">
                            <span class="w-2 h-2 rounded-full bg-rose-500 mr-2"></span>
                            <span class="text-xs font-bold uppercase tracking-widest">Failed Tasks</span>
                        </div>
                        <span class="text-xl font-black {{ $systemInfo['queues']['failed_jobs'] > 0 ? 'text-rose-500' : 'text-slate-500' }}">{{ $systemInfo['queues']['failed_jobs'] }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection