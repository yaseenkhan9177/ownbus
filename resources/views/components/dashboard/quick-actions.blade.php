@props([
'user' => null,
])

@php
$user = $user ?? auth()->user();
@endphp

{{-- Quick Actions Panel - UAE Fleet ERP --}}
<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end space-y-3" x-data="{ open: false }">

    {{-- Expandable Actions --}}
    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="flex flex-col items-end space-y-2">

        @can('manage-rentals')
        <a href="{{ route('company.rentals.create') }}"
            title="Quick Dispatch"
            class="group flex items-center space-x-2 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-full pl-3 pr-4 py-2.5 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
            <div class="w-7 h-7 rounded-full bg-cyan-500/10 flex items-center justify-center group-hover:bg-cyan-500 transition-colors">
                <svg class="w-3.5 h-3.5 text-cyan-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <span class="text-[11px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap">Quick Dispatch</span>
        </a>
        @endcan

        @can('manage-rentals')
        <a href="{{ route('company.rentals.index') }}?filter=overdue"
            title="Emergency Alert"
            class="group flex items-center space-x-2 bg-white dark:bg-slate-800 border border-rose-100 dark:border-rose-900/30 rounded-full pl-3 pr-4 py-2.5 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
            <div class="w-7 h-7 rounded-full bg-rose-500/10 flex items-center justify-center group-hover:bg-rose-500 transition-colors">
                <svg class="w-3.5 h-3.5 text-rose-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-[11px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap">Emergency Alert</span>
        </a>
        @endcan

        @can('manage-contracts')
        <a href="{{ route('company.rentals.contract') }}"
            title="Create Contract"
            class="group flex items-center space-x-2 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-full pl-3 pr-4 py-2.5 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
            <div class="w-7 h-7 rounded-full bg-violet-500/10 flex items-center justify-center group-hover:bg-violet-500 transition-colors">
                <svg class="w-3.5 h-3.5 text-violet-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="text-[11px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap">Create Contract</span>
        </a>
        @endcan

        @can('manage-payments')
        <a href="{{ route('company.payments.create') }}"
            title="Record Payment"
            class="group flex items-center space-x-2 bg-white dark:bg-slate-800 border border-emerald-100 dark:border-emerald-900/30 rounded-full pl-3 pr-4 py-2.5 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
            <div class="w-7 h-7 rounded-full bg-emerald-500/10 flex items-center justify-center group-hover:bg-emerald-500 transition-colors">
                <svg class="w-3.5 h-3.5 text-emerald-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-[11px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap">Record Payment</span>
        </a>
        @endcan

    </div>

    {{-- Toggle Button --}}
    <button @click="open = !open"
        class="w-14 h-14 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 shadow-2xl shadow-cyan-500/40 hover:shadow-cyan-500/60 flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 focus:outline-none"
        :class="{ 'rotate-45': open }">
        <svg class="w-6 h-6 text-white transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
    </button>

    {{-- Tooltip label --}}
    <span x-show="!open" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mr-1">Actions</span>

</div>