@extends('layouts.company')

@section('content')
<div class="relative min-h-screen pb-12">
    <!-- Premium Page Glows -->
    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-[500px] h-[500px] bg-blue-600/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-[400px] h-[400px] bg-indigo-600/10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="relative space-y-10">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-bold uppercase tracking-widest mb-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    Customer Management
                </div>
                <h1 class="text-4xl font-black text-white tracking-tight sm:text-5xl">
                    Customer <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400 text-glow-blue-500/30">Directory</span>
                </h1>
                <p class="text-slate-400 text-lg max-w-2xl font-medium leading-relaxed">
                    A centralized intelligence hub for your enterprise clients and independent renters.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('company.customers.create') }}" class="group relative inline-flex items-center gap-3 bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold transition-all hover:bg-blue-500 hover:shadow-[0_0_20px_rgba(37,99,235,0.4)] active:scale-95 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    <svg class="w-6 h-6 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Add New Customer</span>
                </a>
            </div>
        </div>

        <!-- Global Stats Cards (Glassmorphism) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $stats = [
            ['label' => 'Total Revenue', 'value' => 'AED ' . number_format($customers->sum('lifetime_revenue'), 2), 'color' => 'blue', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Outstanding', 'value' => 'AED ' . number_format($customers->sum('current_balance'), 2), 'color' => 'amber', 'icon' => 'M12 11v3m0 0h.01M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Active Now', 'value' => $customers->sum('active_rentals_count'), 'color' => 'emerald', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['label' => 'Total Pool', 'value' => $customers->total(), 'color' => 'indigo', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z']
            ];
            @endphp

            @foreach($stats as $stat)
            <div class="group relative p-6 rounded-[2rem] bg-white/[0.03] border border-white/10 backdrop-blur-md transition-all hover:bg-white/[0.06] hover:-translate-y-1 hover:border-{{ $stat['color'] }}-500/30 overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-{{ $stat['color'] }}-500/5 blur-2xl rounded-full group-hover:bg-{{ $stat['color'] }}-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-{{ $stat['color'] }}-500/10 flex items-center justify-center text-{{ $stat['color'] }}-400 border border-{{ $stat['color'] }}-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-black text-white mt-1 group-hover:text-{{ $stat['color'] }}-400 transition-colors">{{ $stat['value'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Advanced Filter & Search Bar -->
        <div class="relative z-10">
            <div class="p-2 rounded-3xl bg-white/[0.02] border border-white/5 backdrop-blur-3xl shadow-2xl">
                <form action="{{ route('company.customers.index') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
                    <div class="flex-1 relative group">
                        <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                            placeholder="Find by name, unique code, phone..."
                            class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-4 pl-14 pr-4 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600/30 focus:bg-slate-900/80 transition-all font-medium">
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-2">
                        <div class="w-full sm:w-48 relative">
                            <select name="type" class="w-full appearance-none bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-blue-600/30 transition-all cursor-pointer">
                                <option value="">All Types</option>
                                <option value="individual" {{ ($filters['type'] ?? '') == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="corporate" {{ ($filters['type'] ?? '') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <div class="w-full sm:w-48 relative">
                            <select name="status" class="w-full appearance-none bg-slate-900/50 border border-white/5 rounded-2xl py-4 px-6 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-blue-600/30 transition-all cursor-pointer">
                                <option value="">All Statuses</option>
                                <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="blacklisted" {{ ($filters['status'] ?? '') == 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                                <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-white/[0.05] hover:bg-white/[0.1] border border-white/10 rounded-2xl text-white font-bold transition-all active:scale-95">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Premium Table Section -->
        <div class="bg-white/[0.02] border border-white/10 rounded-[2.5rem] overflow-hidden shadow-[0_32px_64px_-16px_rgba(0,0,0,0.5)] backdrop-blur-sm">
            <div class="overflow-x-auto overflow-y-visible">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.02] border-b border-white/10">
                            <th class="pl-10 pr-6 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Partner Entity</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Profile Type</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Health Score</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Exposure</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Utilization</th>
                            <th class="pl-6 pr-10 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($customers as $customer)
                        @php
                        $gradientColors = [
                        'blue' => 'from-blue-500 to-indigo-500',
                        'green' => 'from-emerald-500 to-teal-500',
                        'purple' => 'from-purple-500 to-pink-500',
                        'orange' => 'from-orange-500 to-yellow-500',
                        ];
                        $colorKey = array_keys($gradientColors)[$customer->id % count($gradientColors)];
                        $grad = $gradientColors[$colorKey];
                        @endphp
                        <tr class="group hover:bg-white/[0.03] transition-all duration-300">
                            <td class="pl-10 pr-6 py-6">
                                <div class="flex items-center gap-5">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $grad }} p-[2px] shadow-lg shadow-{{ $colorKey }}-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500">
                                            <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center font-black text-transparent bg-clip-text bg-gradient-to-br {{ $grad }} text-xl">
                                                {{ substr($customer->name, 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-slate-900 {{ $customer->type === 'corporate' ? 'bg-indigo-500' : 'bg-emerald-500' }} flex items-center justify-center shadow-lg">
                                            @if($customer->type === 'corporate')
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12V12a1 1 0 001 1h8a1 1 0 001-1V10.12l1.69-.724a1 1 0 011.332.551l1 3a1 1 0 01-.122.862l-7 10a1 1 0 01-1.643 0l-7-10a1 1 0 01-.122-.862l1-3a1 1 0 011.333-.55z" />
                                            </svg>
                                            @else
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="text-base font-bold text-white group-hover:text-blue-400 transition-colors">{{ $customer->name }}</h3>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $customer->customer_code }}</span>
                                            <span class="w-1 h-1 rounded-full bg-slate-800"></span>
                                            <span class="text-[10px] font-bold text-slate-400 tracking-wide">{{ $customer->phone }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 border-none">
                                <div class="inline-flex rounded-xl bg-white/[0.03] border border-white/10 p-1">
                                    <span class="px-4 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $customer->type === 'corporate' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                        {{ $customer->type }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-6 border-none">
                                <div class="flex items-center gap-3">
                                    @if($customer->risk_level === 'red')
                                    <div class="px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                                        <span class="text-[10px] font-black text-red-400 uppercase tracking-tighter">At Risk</span>
                                    </div>
                                    @elseif($customer->risk_level === 'yellow')
                                    <div class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                                        <span class="text-[10px] font-black text-amber-400 uppercase tracking-tighter">High Exposure</span>
                                    </div>
                                    @else
                                    <div class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-tighter">Prime</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-6 border-none">
                                <div class="space-y-1">
                                    <div class="text-sm font-black {{ $customer->current_balance > 0 ? 'text-amber-400' : 'text-slate-400' }}">
                                        AED {{ number_format($customer->current_balance, 2) }}
                                    </div>
                                    <div class="w-full bg-white/5 h-1 rounded-full overflow-hidden">
                                        @php
                                        $percent = $customer->credit_limit > 0 ? min(100, ($customer->current_balance / $customer->credit_limit) * 100) : 0;
                                        $barColor = $percent > 80 ? 'bg-red-500' : ($percent > 50 ? 'bg-amber-500' : 'bg-blue-500');
                                        @endphp
                                        <div class="{{ $barColor }} h-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Limit: {{ number_format($customer->credit_limit, 0) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-6 border-none">
                                <div class="flex items-end gap-2">
                                    <span class="text-xl font-black text-white leading-none">{{ $customer->active_rentals_count }}</span>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider pb-0.5">Active Units</span>
                                </div>
                                <div class="mt-1 flex items-center gap-1.5">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <span class="text-[9px] font-black text-slate-400 uppercase">AED {{ number_format($customer->lifetime_revenue, 0) }} LTV</span>
                                </div>
                            </td>
                            <td class="pl-6 pr-10 py-6 text-right border-none">
                                <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-4 group-hover:translate-x-0">
                                    <a href="{{ route('company.customers.show', $customer) }}" class="w-10 h-10 rounded-xl bg-white/[0.05] border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-blue-600 hover:border-blue-500 hover:shadow-[0_0_15px_rgba(37,99,235,0.3)] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('company.customers.edit', $customer) }}" class="w-10 h-10 rounded-xl bg-white/[0.05] border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-indigo-600 hover:border-indigo-500 hover:shadow-[0_0_15px_rgba(79,70,229,0.3)] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-32 text-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-blue-500/5 to-transparent"></div>
                                <div class="relative flex flex-col items-center">
                                    <div class="w-24 h-24 rounded-full bg-white/[0.02] border border-white/5 flex items-center justify-center mb-8 shadow-inner">
                                        <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-3xl font-black text-white mb-3">Void of Data</h3>
                                    <p class="text-slate-500 text-lg max-w-sm mb-10 font-medium">Your enterprise roster is currently empty. Initialize your first partnership now.</p>
                                    <a href="{{ route('company.customers.create') }}" class="inline-flex items-center gap-3 bg-white text-slate-900 px-10 py-4 rounded-2xl font-black hover:bg-blue-400 hover:text-white transition-all shadow-xl active:scale-95">
                                        Onboard First Customer
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
            <div class="px-10 py-10 border-t border-white/5 bg-white/[0.01]">
                {{ $customers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }

    .animate-shimmer {
        animation: shimmer 2s infinite;
    }

    /* Simple glow effects using utility classes were added, but adding some custom depth here */
    .text-glow-blue-500\/30 {
        text-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
</style>
@endsection