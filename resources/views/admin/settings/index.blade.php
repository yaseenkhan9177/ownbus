@extends('layouts.super-admin')

@section('title', 'Platform Settings | SaaS Admin')

@section('header_title')
<div class="flex items-center space-x-4">
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)] flex items-center">
        <svg class="h-6 w-6 mr-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Global Configuration
    </h1>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ activeTab: 'general' }">

    @if(session('success'))
    <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-xl flex items-center shadow-lg animate-fade-in-down">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-bold tracking-wide">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex flex-col md:flex-row gap-8 mt-6">

        <!-- Left Sidebar Navigation -->
        <div class="w-full md:w-64 shrink-0">
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden sticky top-6">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/50">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Configuration Groups</h3>
                </div>
                <!-- Menu -->
                <nav class="p-3 space-y-1">
                    @foreach(['general' => 'Control Center', 'branding' => 'UI Cosmetics', 'mail' => 'SMTP Routing', 'advanced' => 'System Physics'] as $groupKey => $groupName)
                    <button @click="activeTab = '{{ $groupKey }}'"
                        :class="{ 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : activeTab === '{{ $groupKey }}', 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' : activeTab !== '{{ $groupKey }}' }"
                        class="w-full flex items-center px-4 py-3 text-sm font-semibold rounded-lg border transition-all duration-300 text-left">
                        @if($groupKey === 'general')
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        @elseif($groupKey === 'branding')
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        @elseif($groupKey === 'mail')
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        @elseif($groupKey === 'advanced')
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        @endif
                        {{ $groupName }}
                    </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Right Configuration Panels -->
        <div class="flex-1">
            <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden min-h-[500px] relative">

                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    @foreach($groupedSettings as $groupKey => $settings)
                    <div x-show="activeTab === '{{ $groupKey }}'"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="p-0"
                        style="display: none;">

                        <!-- Panel Header -->
                        <div class="px-8 py-6 border-b border-slate-800 bg-slate-900/50">
                            <h2 class="text-xl font-bold text-slate-200 capitalize tracking-wide">{{ $groupKey }} Settings</h2>
                            <p class="text-xs text-slate-500 mt-1 font-mono">Modifying these vectors alters global application physics.</p>
                        </div>

                        <!-- Form Fields -->
                        <div class="p-8 space-y-8">
                            @foreach($settings as $setting)
                            <div>
                                <label class="block text-sm font-bold text-slate-300 uppercase tracking-widest mb-2 flex items-center justify-between">
                                    <span>{{ str_replace('_', ' ', $setting->key) }}</span>
                                    <span class="text-[10px] text-slate-600 font-mono font-normal">KEY: {{ $setting->key }}</span>
                                </label>

                                @if($setting->type === 'string')
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                    class="w-full bg-slate-900 border border-slate-700 text-slate-300 rounded-lg focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 block p-3 transition-colors shadow-inner font-mono text-sm">

                                @elseif($setting->type === 'integer')
                                <input type="number" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                    class="w-full bg-slate-900 border border-slate-700 text-slate-300 rounded-lg focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 block p-3 transition-colors shadow-inner font-mono text-sm">

                                @elseif($setting->type === 'text')
                                <textarea name="{{ $setting->key }}" rows="4"
                                    class="w-full bg-slate-900 border border-slate-700 text-slate-300 rounded-lg focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 block p-3 transition-colors shadow-inner font-mono text-sm resize-none">{{ $setting->value }}</textarea>

                                @elseif($setting->type === 'boolean')
                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer mt-2 group">
                                    <input type="checkbox" name="{{ $setting->key }}" value="1" class="sr-only peer" {{ $setting->value == '1' ? 'checked' : '' }}>
                                    <div class="w-14 h-7 bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-cyan-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-cyan-600 border border-slate-600 shadow-inner group-hover:border-cyan-500/50"></div>
                                    <span class="ml-4 text-sm font-semibold text-slate-400 peer-checked:text-cyan-400 uppercase tracking-widest transition-colors">{{ $setting->value == '1' ? 'Active / Enabled' : 'Disabled' }}</span>
                                </label>
                                @endif

                            </div>
                            @endforeach
                        </div>

                    </div>
                    @endforeach

                    <!-- Sticky Footer Actions -->
                    <div class="absolute bottom-0 inset-x-0 px-8 py-5 border-t border-slate-800 bg-slate-900/90 backdrop-blur-sm flex justify-end">
                        <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-2.5 px-8 rounded-lg shadow-[0_0_15px_rgba(6,182,212,0.4)] transition-all flex items-center uppercase tracking-widest text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Save Active Sequence
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection