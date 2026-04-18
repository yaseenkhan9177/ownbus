@extends('layouts.company')

@section('content')
<div class="space-y-6">
    <!-- Tactical Personnel Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1">PERSONNEL_MONITOR</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Tactical Driver Fleet Overview</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('company.reports.export.drivers.pdf', request()->all()) }}" class="p-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" title="Export PDF">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </a>
            <a href="{{ route('company.reports.export.drivers.excel', request()->all()) }}" class="p-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" title="Export Excel">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </a>
            <button onclick="window.print()" type="button" class="p-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" title="Print">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
            </button>
            <a href="{{ route('company.drivers.create') }}" class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-tighter hover:scale-105 transition-all">
                + REGISTER_PERSONNEL
            </a>
        </div>
    </div>

    <!-- Operations Toolbar -->
    <div class="bg-white dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-2xl p-4">
        <form action="{{ route('company.drivers.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px] relative">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="SEARCH_PERSONNEL_ID_OR_NAME..."
                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl pl-11 pr-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div class="flex items-center gap-3">
                <select name="status" class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                    <option value="">ALL_STATUSES</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ACTIVE</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>SUSPENDED</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>INACTIVE</option>
                </select>

                <label class="flex items-center gap-2 px-4 py-3 bg-slate-50 dark:bg-slate-800 rounded-xl cursor-pointer">
                    <input type="checkbox" name="license_expiring_soon" value="1" {{ request('license_expiring_soon') ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">EXPIRY_ALERT</span>
                </label>

                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all">
                    FILTER
                </button>
            </div>
        </form>
    </div>

    <!-- Driver Fleet Grid -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Personnel</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Protocol_State</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Compliance_Expiry</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Base_Station</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Dossier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($drivers as $driver)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 font-bold group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 group-hover:text-blue-500 transition-all">
                                    {{ strtoupper(substr($driver->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $driver->name }}</div>
                                    <div class="text-[10px] font-bold text-slate-400">{{ $driver->driver_code }} • {{ $driver->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                @php
                                $status = $driver->status;
                                $styles = [
                                'active' => 'bg-emerald-500/10 text-emerald-500 ring-emerald-500/20',
                                'suspended' => 'bg-rose-500/10 text-rose-500 ring-rose-500/20',
                                'inactive' => 'bg-slate-500/10 text-slate-500 ring-slate-500/20',
                                ];
                                $style = $styles[$status] ?? 'bg-slate-500/10 text-slate-500 ring-slate-500/20';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest ring-1 {{ $style }}">
                                    {{ $status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $daysRemaining = now()->diffInDays($driver->license_expiry_date, false);
                            @endphp
                            <div class="flex flex-col">
                                <span class="text-xs font-black {{ $daysRemaining < 0 ? 'text-rose-500' : ($daysRemaining < 30 ? 'text-amber-500' : 'text-slate-900 dark:text-white') }}">
                                    {{ $driver->license_expiry_date->format('d M Y') }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-400">
                                    @if($daysRemaining < 0)
                                        PROTOCOL_EXPIRED
                                        @elseif($daysRemaining < 30)
                                        EXPIRING_SOON ({{ $daysRemaining }}D)
                                        @else
                                        VALID ({{ $daysRemaining }}D)
                                        @endif
                                        </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase">
                                {{ $driver->branch->name ?? 'CENTRAL_COMMAND' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('company.drivers.show', $driver) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-blue-500 hover:text-blue-600 uppercase tracking-widest transition-all">
                                VIEW_DOSSIER
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-200 dark:text-slate-700">
                                    <i class="bi bi-person-slash text-3xl"></i>
                                </div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ZERO_PERSONNEL_DETECTED</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($drivers->hasPages())
        <div class="px-6 py-4 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $drivers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection