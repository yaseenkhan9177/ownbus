@extends('layouts.company')

@section('title', 'Vendors & Suppliers - AP Control')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Vendors & Suppliers</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500">

    {{-- 1. Toolbar & Stats --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total Vendors</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $vendors->total() }}</p>
            </div>
            <div class="p-3 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest leading-none mb-1">Active</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $vendors->where('status', 'active')->count() }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('company.vendors.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-500/20 transition-all flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Register Vendor
            </a>
        </div>
    </div>

    {{-- 2. Advanced Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
        <form action="{{ route('company.vendors.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-10 py-2.5 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500"
                    placeholder="Search Name, Code, Phone...">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select name="status" class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <div class="flex items-center space-x-2">
                <button type="submit" class="p-2.5 bg-slate-900 dark:bg-slate-800 text-white rounded-xl hover:bg-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </button>
                <a href="{{ route('company.vendors.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-blue-500">Reset</a>
            </div>
        </form>
    </div>

    {{-- 3. Vendors Table --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                        <th class="py-4 px-6">Vendor Identification</th>
                        <th class="py-4 px-4">Contact Logic</th>
                        <th class="py-4 px-4">Account Status</th>
                        <th class="py-4 px-4 text-right">Outstanding Bal</th>
                        <th class="py-4 px-6 text-right">Command</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @forelse($vendors as $vendor)
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold shrink-0 border border-blue-200 dark:border-blue-800">
                                    {{ substr($vendor->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('company.vendors.show', $vendor) }}" class="text-sm font-black text-slate-900 dark:text-white group-hover:text-blue-500 transition-colors uppercase tracking-tight">
                                        {{ $vendor->name }}
                                    </a>
                                    <div class="flex items-center text-[10px] text-slate-500 mt-0.5 space-x-2">
                                        <span class="font-bold">Code: {{ $vendor->vendor_code }}</span>
                                        @if($vendor->tax_number)
                                        <span>&bull;</span>
                                        <span>Tax: {{ $vendor->tax_number }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $vendor->phone ?? 'N/A' }}</span>
                                <span class="text-[10px] text-slate-500 truncate max-w-[150px]">{{ $vendor->email ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            @php
                            $isSuspended = $vendor->isSuspended();
                            $color = $isSuspended ? 'rose' : 'emerald';
                            $label = $isSuspended ? 'Suspended' : 'Operational';
                            @endphp
                            <div class="inline-flex items-center px-2 py-1 rounded-lg bg-{{ $color }}-500/10 text-{{ $color }}-500 space-x-1.5 border border-current/10">
                                <div class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500 {{ !$isSuspended ? 'animate-pulse' : '' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $label }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            @php
                            $balance = $vendor->calculateOutstandingBalance();
                            @endphp
                            <span class="text-xs font-black {{ $balance > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                AED {{ number_format($balance, 2) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('company.vendors.show', $vendor) }}" class="p-2 text-slate-400 hover:text-blue-500 transition-colors" title="Open Ledger">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('company.vendors.edit', $vendor) }}" class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors" title="Modify">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V10H2V20h5M10 8V6a2 2 0 012-2h0a2 2 0 012 2v2M8 12h8"></path>
                                </svg>
                                <p class="text-sm font-bold uppercase tracking-widest">No vendors recognized in your database</p>
                                <a href="{{ route('company.vendors.create') }}" class="text-blue-500 hover:underline mt-2 text-xs font-black uppercase tracking-tighter">Add Vendor Intelligence &rarr;</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
        <div class="px-6 py-4 border-t border-gray-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $vendors->links() }}
        </div>
        @endif
    </div>
</div>
@endsection