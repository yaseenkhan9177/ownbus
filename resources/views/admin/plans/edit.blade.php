@extends('layouts.super-admin')

@section('title', 'Edit Subscription Plan | SaaS Admin')

@section('header_title')
<div class="flex items-center space-x-4">
    <a href="{{ route('admin.plans.index') }}" class="text-slate-400 hover:text-cyan-400 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
    </a>
    <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)]">
        Modifying Structure: <span class="text-purple-400">{{ $plan->name }}</span>
    </h1>
</div>
@endsection

@section('content')
<div class="max-w-4xl">

    @if ($errors->any())
    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-lg mb-6">
        <div class="flex items-center mb-2">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-bold uppercase tracking-wider text-sm">Action Failed</span>
        </div>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.plans.update', $plan->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Core Details -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-6 uppercase tracking-wider flex items-center">
                <svg class="h-5 w-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Core Settings
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Internal Plan Name</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-medium">
                </div>

                <!-- Monthly Price -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Monthly Price (USD)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500">$</span>
                        </div>
                        <input type="number" step="0.01" name="price_monthly" value="{{ old('price_monthly', $plan->price_monthly) }}" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg pl-8 pr-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-mono">
                    </div>
                </div>

                <!-- Yearly Price -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Yearly Price (USD)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500">$</span>
                        </div>
                        <input type="number" step="0.01" name="price_yearly" value="{{ old('price_yearly', $plan->price_yearly) }}" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg pl-8 pr-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-mono">
                    </div>
                </div>

                <!-- Trial Days -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Trial Period (Days)</label>
                    <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" required min="0" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-mono">
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center h-full pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                        <div class="w-14 h-7 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.2)]"></div>
                        <span class="ml-3 text-sm font-bold text-slate-300 uppercase tracking-wider">Plan Active</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Limits / Features (JSON Array mapping) -->
        @php
        // Extract existing features to default values
        $currentMaxVehicles = isset($plan->features['max_vehicles']) && $plan->features['max_vehicles'] == 99999 ? -1 : ($plan->features['max_vehicles'] ?? 10);
        $currentMaxUsers = isset($plan->features['max_users']) && $plan->features['max_users'] == 99999 ? -1 : ($plan->features['max_users'] ?? 5);
        @endphp

        <div class="bg-[#0f1524] rounded-xl border border-slate-800 p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-slate-100 border-b border-slate-800 pb-3 mb-6 uppercase tracking-wider flex items-center">
                <svg class="h-5 w-5 mr-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                System Limits & Features
            </h3>

            <p class="text-xs text-slate-500 mb-6 italic">Set to <code class="bg-slate-800 px-1 py-0.5 rounded text-amber-400 font-mono">-1</code> to allow <strong>UNLIMITED</strong> capacity for that specific module limit.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Vehicle Limit -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Max Fleet Vehicles</label>
                    <input type="number" name="max_vehicles" value="{{ old('max_vehicles', $currentMaxVehicles) }}" required min="-1" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all font-mono">
                </div>

                <!-- User Limit -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Max System Users / Drivers</label>
                    <input type="number" name="max_users" value="{{ old('max_users', $currentMaxUsers) }}" required min="-1" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all font-mono">
                </div>
            </div>

            <!-- Toggles for JSON features -->
            <div class="pt-6 border-t border-slate-800/80">
                <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-4">Module Access</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center p-4 bg-slate-800/30 border border-slate-700 rounded-lg cursor-pointer hover:bg-slate-800/50 transition-colors">
                        <input type="checkbox" name="features[api_access]" value="1" class="w-4 h-4 text-purple-600 bg-slate-900 border-slate-700 rounded focus:ring-purple-500 focus:ring-2" {{ old('features.api_access', $plan->features['api_access'] ?? false) ? 'checked' : '' }}>
                        <span class="ml-3 text-sm font-medium text-slate-300">API Access</span>
                    </label>

                    <label class="flex items-center p-4 bg-slate-800/30 border border-slate-700 rounded-lg cursor-pointer hover:bg-slate-800/50 transition-colors">
                        <input type="checkbox" name="features[custom_domain]" value="1" class="w-4 h-4 text-purple-600 bg-slate-900 border-slate-700 rounded focus:ring-purple-500 focus:ring-2" {{ old('features.custom_domain', $plan->features['custom_domain'] ?? false) ? 'checked' : '' }}>
                        <span class="ml-3 text-sm font-medium text-slate-300">Custom Domain Mapping</span>
                    </label>

                    <label class="flex items-center p-4 bg-slate-800/30 border border-slate-700 rounded-lg cursor-pointer hover:bg-slate-800/50 transition-colors">
                        <input type="checkbox" name="features[white_label]" value="1" class="w-4 h-4 text-purple-600 bg-slate-900 border-slate-700 rounded focus:ring-purple-500 focus:ring-2" {{ old('features.white_label', $plan->features['white_label'] ?? false) ? 'checked' : '' }}>
                        <span class="ml-3 text-sm font-medium text-slate-300">White-label Branding</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end pt-4 border-t border-slate-800">
            <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-lg shadow-[0_0_15px_rgba(147,51,234,0.4)] transition-all uppercase tracking-widest text-sm">
                Save & Update Plan Architecture
            </button>
        </div>

    </form>
</div>
@endsection