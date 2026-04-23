@extends('layouts.company')

@section('title', 'Upgrade Subscription')

@section('header_title')
<h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest">Upgrade Your OwnBus Plan</h1>
@endsection

@section('content')
<div x-data="{ cycle: 'monthly' }" class="max-w-6xl mx-auto space-y-12 pb-12">
    
    <div class="text-center space-y-4">
        <h2 class="text-3xl font-bold text-white">Choose the plan that fits your fleet</h2>
        <p class="text-slate-400 max-w-2xl mx-auto">Scale your operations with our enterprise-grade features. No hidden fees, cancel anytime.</p>
        
        <!-- Toggle -->
        <div class="flex items-center justify-center mt-8">
            <div class="bg-slate-900 p-1 rounded-xl border border-slate-700 flex items-center relative">
                <button @click="cycle = 'monthly'" :class="cycle === 'monthly' ? 'bg-slate-700 text-white shadow-sm' : 'text-slate-400 hover:text-slate-300'" class="px-6 py-2 rounded-lg text-sm font-bold uppercase tracking-wider transition-all z-10 relative">
                    Monthly
                </button>
                <button @click="cycle = 'yearly'" :class="cycle === 'yearly' ? 'bg-cyan-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-300'" class="px-6 py-2 rounded-lg text-sm font-bold uppercase tracking-wider transition-all z-10 relative flex items-center">
                    Yearly
                    <span class="ml-2 bg-yellow-400 text-yellow-900 text-[9px] px-1.5 py-0.5 rounded-sm font-black">SAVE 20%</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="grid md:grid-cols-3 gap-8">
        
        <!-- Starter Plan -->
        <div class="bg-[#111827] border border-slate-700 rounded-3xl p-8 flex flex-col relative transition-all hover:border-slate-500">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-white mb-2">Starter</h3>
                <p class="text-sm text-slate-400">Perfect for small fleets getting started.</p>
            </div>
            <div class="mb-6">
                <div class="flex items-baseline text-4xl font-black text-white">
                    <span class="text-lg text-slate-400 mr-1 font-bold">AED</span>
                    <span x-text="cycle === 'monthly' ? '99' : '999'"></span>
                </div>
                <div class="text-sm text-slate-500 mt-1" x-text="cycle === 'monthly' ? '/ month' : '/ year'"></div>
            </div>
            
            <ul class="space-y-4 flex-1 mb-8">
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Up to 10 vehicles</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Basic reports</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Email support</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">GPS Tracking</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Invoicing System</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Fine Management</span></li>
                <li class="flex items-start opacity-40"><span class="text-slate-600 mr-3">✗</span> <span class="text-slate-500 text-sm">WhatsApp Integration</span></li>
                <li class="flex items-start opacity-40"><span class="text-slate-600 mr-3">✗</span> <span class="text-slate-500 text-sm">API Access</span></li>
            </ul>

            <a :href="`https://wa.me/{{ preg_replace('/[^0-9]/', '', env('OWNER_WHATSAPP', '+923409172223')) }}?text=Hi, I want to subscribe to OwnBus. My company: {{ urlencode(auth()->user()->company->name ?? '') }}. Plan: Starter (${cycle})`" target="_blank" class="block w-full py-3 px-4 bg-slate-800 hover:bg-slate-700 text-white text-center rounded-xl font-bold uppercase tracking-widest text-sm transition-colors border border-slate-600">
                Contact Us
            </a>
        </div>

        <!-- Growth Plan -->
        <div class="bg-gradient-to-b from-cyan-900/40 to-slate-900 border-2 border-cyan-500 rounded-3xl p-8 flex flex-col relative transform md:-translate-y-4 shadow-[0_0_30px_rgba(6,182,212,0.15)]">
            <div class="absolute top-0 inset-x-0 transform -translate-y-1/2 flex justify-center">
                <span class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white text-xs font-black uppercase tracking-widest py-1.5 px-4 rounded-full shadow-lg">⭐ Most Popular</span>
            </div>
            <div class="mb-6 mt-2">
                <h3 class="text-xl font-bold text-cyan-400 mb-2">Growth</h3>
                <p class="text-sm text-slate-300">For growing operations needing automation.</p>
            </div>
            <div class="mb-6">
                <div class="flex items-baseline text-4xl font-black text-white">
                    <span class="text-lg text-cyan-500 mr-1 font-bold">AED</span>
                    <span x-text="cycle === 'monthly' ? '199' : '1999'"></span>
                </div>
                <div class="text-sm text-cyan-200/60 mt-1" x-text="cycle === 'monthly' ? '/ month' : '/ year'"></div>
            </div>
            
            <ul class="space-y-4 flex-1 mb-8">
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-200 text-sm font-medium">Up to 30 vehicles</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-200 text-sm font-medium">Full analytics & reports</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-200 text-sm font-medium">Priority support</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">GPS Tracking</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Invoicing System</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Fine Management</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-200 text-sm font-medium text-cyan-300">WhatsApp Integration</span></li>
                <li class="flex items-start opacity-40"><span class="text-slate-600 mr-3">✗</span> <span class="text-slate-500 text-sm">API Access</span></li>
            </ul>

            <a :href="`https://wa.me/{{ preg_replace('/[^0-9]/', '', env('OWNER_WHATSAPP', '+923409172223')) }}?text=Hi, I want to subscribe to OwnBus. My company: {{ urlencode(auth()->user()->company->name ?? '') }}. Plan: Growth (${cycle})`" target="_blank" class="block w-full py-3 px-4 bg-cyan-500 hover:bg-cyan-400 text-slate-900 text-center rounded-xl font-bold uppercase tracking-widest text-sm transition-colors shadow-lg">
                Contact Us
            </a>
        </div>

        <!-- Enterprise Plan -->
        <div class="bg-[#111827] border border-slate-700 rounded-3xl p-8 flex flex-col relative transition-all hover:border-slate-500">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-white mb-2">Enterprise</h3>
                <p class="text-sm text-slate-400">Maximum power for large scale fleets.</p>
            </div>
            <div class="mb-6">
                <div class="flex items-baseline text-4xl font-black text-white">
                    <span class="text-lg text-slate-400 mr-1 font-bold">AED</span>
                    <span x-text="cycle === 'monthly' ? '399' : '3999'"></span>
                </div>
                <div class="text-sm text-slate-500 mt-1" x-text="cycle === 'monthly' ? '/ month' : '/ year'"></div>
            </div>
            
            <ul class="space-y-4 flex-1 mb-8">
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Unlimited vehicles</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">All premium features</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Dedicated account manager</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">GPS Tracking</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Invoicing System</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">Fine Management</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-slate-300 text-sm">WhatsApp Integration</span></li>
                <li class="flex items-start"><span class="text-cyan-400 mr-3">✓</span> <span class="text-white text-sm font-medium">Full API Access</span></li>
            </ul>

            <a :href="`https://wa.me/{{ preg_replace('/[^0-9]/', '', env('OWNER_WHATSAPP', '+923409172223')) }}?text=Hi, I want to subscribe to OwnBus. My company: {{ urlencode(auth()->user()->company->name ?? '') }}. Plan: Enterprise (${cycle})`" target="_blank" class="block w-full py-3 px-4 bg-slate-800 hover:bg-slate-700 text-white text-center rounded-xl font-bold uppercase tracking-widest text-sm transition-colors border border-slate-600">
                Contact Us
            </a>
        </div>
    </div>

    <!-- Contact & Activation Info -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 md:p-10 mt-12 max-w-4xl mx-auto shadow-xl">
        <div class="flex flex-col md:flex-row gap-10">
            <div class="flex-1">
                <h3 class="text-lg font-bold text-white uppercase tracking-widest mb-6 flex items-center">
                    <span class="text-2xl mr-3">📞</span> How to Subscribe
                </h3>
                <ul class="space-y-4">
                    <li class="flex items-center text-slate-300 text-sm">
                        <span class="w-6 h-6 rounded-full bg-cyan-900/50 text-cyan-400 flex items-center justify-center font-bold mr-3 border border-cyan-500/30">1</span>
                        Choose your preferred plan above
                    </li>
                    <li class="flex items-center text-slate-300 text-sm">
                        <span class="w-6 h-6 rounded-full bg-cyan-900/50 text-cyan-400 flex items-center justify-center font-bold mr-3 border border-cyan-500/30">2</span>
                        Contact us via WhatsApp or Email
                    </li>
                    <li class="flex items-center text-slate-300 text-sm">
                        <span class="w-6 h-6 rounded-full bg-cyan-900/50 text-cyan-400 flex items-center justify-center font-bold mr-3 border border-cyan-500/30">3</span>
                        We manually activate your account instantly
                    </li>
                    <li class="flex items-center text-slate-300 text-sm">
                        <span class="w-6 h-6 rounded-full bg-cyan-900/50 text-cyan-400 flex items-center justify-center font-bold mr-3 border border-cyan-500/30">4</span>
                        Start managing your fleet!
                    </li>
                </ul>
            </div>
            
            <div class="flex-1 bg-[#0A0F1E] rounded-2xl p-6 border border-slate-800">
                <div class="space-y-4 mb-6">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">📧</span>
                        <span class="text-white font-medium text-sm">{{ env('OWNER_EMAIL', 'ykcaptain2223@gmail.com') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xl">📱</span>
                        <span class="text-white font-medium text-sm">{{ env('OWNER_WHATSAPP', '+923409172223') }}</span>
                    </div>
                </div>
                
                <div class="flex gap-3 mb-4">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', env('OWNER_WHATSAPP', '+923409172223')) }}" target="_blank" class="flex-1 py-2 px-3 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-lg text-sm font-bold flex items-center justify-center transition-colors">
                        💬 WhatsApp Us
                    </a>
                    <a href="mailto:{{ env('OWNER_EMAIL', 'ykcaptain2223@gmail.com') }}?subject=OwnBus%20Subscription" class="flex-1 py-2 px-3 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm font-bold flex items-center justify-center transition-colors">
                        📧 Email Us
                    </a>
                </div>
                
                <div class="text-[11px] text-emerald-400 font-bold uppercase tracking-wider text-center flex items-center justify-center bg-emerald-900/20 py-2 rounded-lg border border-emerald-500/20">
                    <span class="mr-2">⚡</span> Activation within 24 hours
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
