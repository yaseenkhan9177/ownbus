@extends('layouts.super-admin')

@section('title', 'Support Tickets | SaaS Admin')

@section('header_title')
<div class="flex items-center justify-between w-full">
    <div class="flex items-center space-x-4">
        <h1 class="text-xl font-bold text-slate-100 uppercase tracking-widest text-shadow-[0_0_10px_rgba(255,255,255,0.3)] flex items-center">
            <svg class="h-6 w-6 mr-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            Support Command Center
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Ticket KPI Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

        <a href="{{ route('admin.support.index', ['status' => 'all']) }}" class="bg-[#0f1524] rounded-xl border {{ $status === 'all' ? 'border-cyan-500 shadow-[0_0_15px_rgba(6,182,212,0.3)]' : 'border-slate-800' }} p-4 hover:border-slate-600 transition-all flex flex-col justify-center items-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total</p>
            <h3 class="text-2xl font-black text-slate-200">
                {{ $counts['open'] + $counts['in_progress'] + $counts['resolved'] + $counts['closed'] }}
            </h3>
        </a>

        <a href="{{ route('admin.support.index', ['status' => 'open']) }}" class="bg-[#0f1524] rounded-xl border {{ $status === 'open' ? 'border-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.3)]' : 'border-slate-800' }} p-4 hover:border-amber-500/50 transition-all flex flex-col justify-center items-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Open</p>
            <h3 class="text-2xl font-black text-amber-500">{{ $counts['open'] }}</h3>
        </a>

        <a href="{{ route('admin.support.index', ['status' => 'in_progress']) }}" class="bg-[#0f1524] rounded-xl border {{ $status === 'in_progress' ? 'border-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.3)]' : 'border-slate-800' }} p-4 hover:border-indigo-500/50 transition-all flex flex-col justify-center items-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">In Progress</p>
            <h3 class="text-2xl font-black text-indigo-400">{{ $counts['in_progress'] }}</h3>
        </a>

        <a href="{{ route('admin.support.index', ['status' => 'resolved']) }}" class="bg-[#0f1524] rounded-xl border {{ $status === 'resolved' ? 'border-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.3)]' : 'border-slate-800' }} p-4 hover:border-emerald-500/50 transition-all flex flex-col justify-center items-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Resolved</p>
            <h3 class="text-2xl font-black text-emerald-400">{{ $counts['resolved'] }}</h3>
        </a>

        <div class="bg-rose-500/10 rounded-xl border border-rose-500/30 p-4 flex flex-col justify-center items-center relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-rose-500/20 group-hover:text-rose-500/40 transition-colors">
                <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest mb-1 relative z-10">Critical Alerts</p>
            <h3 class="text-2xl font-black text-rose-500 relative z-10 animate-pulse">{{ $counts['critical'] }}</h3>
        </div>

    </div>

    <!-- Tickets Data Table -->
    <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center">
            <h2 class="text-sm border-l-2 border-cyan-400 pl-3 font-semibold text-slate-300 uppercase tracking-widest">
                Support Requests
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-4 font-semibold">TKT-ID</th>
                        <th class="px-6 py-4 font-semibold">Tenant Organization</th>
                        <th class="px-6 py-4 font-semibold">Subject / Inquiry</th>
                        <th class="px-6 py-4 font-semibold">Severity</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Last Activity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-800/30 transition-colors group cursor-pointer" onclick="window.location.href='{{ route("admin.support.show", $ticket->id) }}';">

                        <!-- ID -->
                        <td class="px-6 py-4 font-mono text-xs font-bold text-slate-400 group-hover:text-cyan-400 transition-colors">
                            #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}
                        </td>

                        <!-- Organization -->
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded bg-slate-800 flex flex-col items-center justify-center text-xs font-bold text-slate-400 shadow-inner">
                                    {{ substr($ticket->company->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-200">{{ $ticket->company->name }}</div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">Reported by: {{ $ticket->user->name }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Subject -->
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-300 truncate max-w-xs">{{ $ticket->subject }}</div>
                        </td>

                        <!-- Severity -->
                        <td class="px-6 py-4">
                            @if($ticket->priority === 'critical')
                            <span class="px-2.5 py-1 bg-rose-500/20 text-rose-400 border border-rose-500/50 rounded text-[10px] font-black uppercase tracking-widest inline-flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-pulse"></span>
                                Critical
                            </span>
                            @elseif($ticket->priority === 'high')
                            <span class="px-2.5 py-1 bg-orange-500/20 text-orange-400 border border-orange-500/50 rounded text-[10px] font-bold uppercase tracking-widest">
                                High
                            </span>
                            @elseif($ticket->priority === 'medium')
                            <span class="px-2.5 py-1 bg-amber-500/20 text-amber-500 border border-amber-500/50 rounded text-[10px] font-bold uppercase tracking-widest">
                                Medium
                            </span>
                            @else
                            <span class="px-2.5 py-1 bg-slate-800 text-slate-400 border border-slate-700 rounded text-[10px] font-bold uppercase tracking-widest">
                                Low
                            </span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            @if($ticket->status === 'open')
                            <span class="px-2.5 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/30 rounded text-[10px] font-bold uppercase tracking-widest">Open</span>
                            @elseif($ticket->status === 'in_progress')
                            <span class="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/30 rounded text-[10px] font-bold uppercase tracking-widest">In Progress</span>
                            @elseif($ticket->status === 'resolved')
                            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-500 border border-emerald-500/30 rounded text-[10px] font-bold uppercase tracking-widest">Resolved</span>
                            @else
                            <span class="px-2.5 py-1 bg-slate-800 text-slate-500 border border-slate-700 rounded text-[10px] font-bold uppercase tracking-widest">Closed</span>
                            @endif
                        </td>

                        <!-- Last Activity -->
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-semibold text-slate-300">
                                {{ $ticket->last_activity_at ? $ticket->last_activity_at->diffForHumans() : 'Never' }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                {{ $ticket->last_activity_at ? $ticket->last_activity_at->format('M d, Y h:i A') : '-' }}
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <svg class="w-12 h-12 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-slate-500 font-semibold block">No tickets found in this queue.</span>
                            <span class="text-slate-600 text-xs mt-1 block">The support desk is completely clear.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/50">
            {{ $tickets->appends(request()->query())->links() }}
        </div>
        @endif

    </div>

</div>
@endsection