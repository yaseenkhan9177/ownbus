@extends('layouts.company')

@section('title', 'Rental Operations - Tactical Control')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-cyan-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Rental Operations</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500">

    {{-- 1. Tactical Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Fleet Rentals</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $rentals->total() }}</p>
        </div>
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-1">Active Now</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['active'] ?? 0 }}</p>
        </div>
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1">Next 48 Hours</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['upcoming'] ?? 0 }}</p>
        </div>
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm">
            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">Conflicts / Overdue</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['conflicts'] ?? 0 }}</p>
        </div>
    </div>

    {{-- 2. Toolbar & Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
            <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Global Matrix</h2>
            <div class="flex items-center space-x-2">
                <!-- Export UI -->
                <form action="{{ route('company.exports.store') }}" method="POST" class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl p-1 shadow-inner h-9">
                    @csrf
                    <input type="hidden" name="type" value="rentals">
                    <input type="hidden" name="filters[status]" value="{{ request('status') }}">
                    <input type="hidden" name="filters[date_from]" value="{{ request('date_from') }}">
                    <input type="hidden" name="filters[date_to]" value="{{ request('date_to') }}">
                    <select name="format" class="text-[10px] bg-transparent border-none text-slate-700 dark:text-slate-300 font-bold uppercase py-0.5 focus:ring-0 w-20">
                        <option value="xlsx">EXCEL</option>
                        <option value="pdf">PDF</option>
                    </select>
                    <button type="submit" class="px-3 py-1 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow rounded-lg text-[10px] font-black uppercase transition-all flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export
                    </button>
                </form>
                <button onclick="window.print()" type="button" class="p-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" title="Print">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </button>
                <a href="{{ route('company.rentals.create') }}" class="px-4 py-2 bg-slate-900 dark:bg-white dark:text-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-transform flex items-center shadow-lg">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    New Quote / Rental
                </a>
            </div>
        </div>

        <form action="{{ route('company.rentals.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-10 py-2.5 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-cyan-500"
                    placeholder="Search Number or Customer...">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select name="status" class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">

            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 lg:flex-none px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-cyan-500/20 transition-all">
                    Filter
                </button>
                <a href="{{ route('company.rentals.index') }}" class="px-3 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-xl hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    {{-- 3. Rentals Table --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">ID / Number</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Client & Asset</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Period</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Financials</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Ops</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($rentals as $rental)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="bg-slate-900 dark:bg-white dark:text-slate-900 text-white text-[8px] font-black px-2 py-0.5 rounded w-max mb-1 uppercase tracking-tighter">
                                    {{ $rental->rental_number }}
                                </span>
                                <span class="text-xs font-bold text-slate-900 dark:text-white uppercase">{{ $rental->rental_type }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $rental->customer->name }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">
                                    @if($rental->vehicle)
                                    {{ $rental->vehicle->vehicle_number }} / {{ $rental->vehicle->make }}
                                    @else
                                    <span class="text-rose-400 italic font-black uppercase text-[8px]">Pending Assignment</span>
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <div class="flex items-center text-[10px] font-bold text-slate-900 dark:text-white">
                                    <svg class="w-3 h-3 mr-1.5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $rental->start_date->format('d M, H:i') }}
                                </div>
                                <div class="flex items-center text-[10px] text-slate-400 font-medium mt-1">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-11V7" />
                                    </svg>
                                    {{ $rental->end_date->format('d M, H:i') }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-slate-900 dark:text-white tracking-widest uppercase">
                                    {{ number_format($rental->final_amount, 2) }} <span class="text-[8px] text-slate-400 ml-0.5">AED</span>
                                </span>
                                <span class="text-[9px] {{ $rental->payment_status == 'paid' ? 'text-emerald-500' : ($rental->payment_status == 'partial' ? 'text-amber-500' : 'text-rose-500') }} font-black uppercase tracking-tighter">
                                    {{ $rental->payment_status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                            $statusColors = [
                            'draft' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                            'confirmed' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
                            'active' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
                            'completed' => 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400',
                            'cancelled' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
                            ];
                            $color = $statusColors[$rental->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $color }}">
                                {{ $rental->status }}
                            </span>
                            @if(app(\App\Services\DataLockService::class)->isLocked($rental))
                            <div class="mt-1 flex items-center justify-center text-[8px] font-bold text-slate-400 uppercase tracking-tighter" title="{{ app(\App\Services\DataLockService::class)->lockReason($rental) }}">
                                <i class="bi bi-lock-fill mr-1"></i> Locked
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('company.rentals.show', $rental) }}" class="p-2 bg-slate-50 dark:bg-slate-800/50 text-slate-400 hover:text-cyan-500 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @php $isLocked = app(\App\Services\DataLockService::class)->isLocked($rental); @endphp
                                @if($rental->status == 'draft' || $isLocked)
                                <a href="{{ $isLocked ? '#' : route('company.rentals.edit', $rental) }}"
                                    class="p-2 bg-slate-50 dark:bg-slate-800/50 text-slate-400 {{ $isLocked ? 'opacity-50 cursor-not-allowed' : 'hover:text-emerald-500' }} rounded-lg transition-colors"
                                    {!! $isLocked ? 'title="Locked: ' . app(\App\Services\DataLockService::class)->lockReason($rental) . '"' : '' !!}>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.138 2.976a2.25 2.25 0 013.182 3.182L9 15.119l-4 1 1-4 10.138-10.143z" />
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">No Matrix Data Found</h3>
                                <p class="text-[10px] text-slate-400 mt-1">Adjust filters or create a new rental record.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rentals->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 dark:bg-slate-800/50 border-t border-gray-100 dark:border-slate-800">
            {{ $rentals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection