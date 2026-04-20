@extends('portal.layout')

@section('title', 'Fleet Fines Explorer | OwnBus')

@section('content')
<div class="px-6 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Traffic Fines</h1>
            <p class="text-slate-500 text-sm">Monitor and process fleet violations across UAE authorities</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('company.fines.report') }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center border border-indigo-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Fines Report
            </a>
            <a href="{{ route('company.fines.import') }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center border border-indigo-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                Bulk Import
            </a>
            <a href="{{ route('company.fines.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center shadow-lg shadow-indigo-500/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Record New Fine
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Unpaid</p>
            <h3 class="text-2xl font-black text-rose-600">AED {{ number_format($fines->where('status', 'unpaid')->sum('amount'), 2) }}</h3>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Under Appeal</p>
            <h3 class="text-2xl font-black text-amber-500">{{ $fines->where('status', 'appealed')->count() }} Fines</h3>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total black points</p>
            <h3 class="text-2xl font-black text-slate-800">{{ $fines->sum('black_points') }}</h3>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Most Common Violation</p>
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Speeding (80%)</h3>
        </div>
    </div>

    <!-- Fines Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-left">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Authority / Fine #</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Vehicle / Driver</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Violation Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($fines as $fine)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block">{{ $fine->authority }}</span>
                            <span class="text-sm font-bold text-slate-800">{{ $fine->fine_number }}</span>
                            <span class="text-[10px] text-slate-400 block mt-1">{{ $fine->fine_date->format('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800 block">{{ $fine->vehicle->vehicle_number }}</span>
                            <span class="text-xs text-slate-500">{{ $fine->driver->name ?? 'No Driver assigned' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800 block">AED {{ number_format($fine->amount, 2) }}</span>
                            @if($fine->black_points > 0)
                            <span class="text-[10px] text-rose-500 font-bold uppercase tracking-tighter">{{ $fine->black_points }} Black Points</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">
                                {{ $fine->fine_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $colors = [
                                    'unpaid' => 'bg-rose-100 text-rose-600',
                                    'paid' => 'bg-emerald-100 text-emerald-600',
                                    'under-processing' => 'bg-amber-100 text-amber-600',
                                    'appealed' => 'bg-indigo-100 text-indigo-600',
                                    'cancelled' => 'bg-slate-100 text-slate-400'
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colors[$fine->status] ?? 'bg-slate-100' }}">
                                {{ $fine->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('company.fines.show', $fine) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No violations recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection