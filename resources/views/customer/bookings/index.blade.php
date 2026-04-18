@extends('layouts.customer')

@section('title', 'My Bookings')
@section('header_title', 'My Bookings')

@section('content')
<div class="space-y-6">

    {{-- Header + Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-white">Booking History</h2>
            <p class="text-xs text-slate-500 mt-0.5">All your fleet rental bookings in one place</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[140px]">
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Status</label>
            <select name="status" class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-blue-500">
                <option value="">All Statuses</option>
                @foreach(['confirmed','active','completed','closed','cancelled','overdue'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">From</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">To</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl transition">Filter</button>
            <a href="{{ route('portal.bookings.index') }}" class="px-5 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-bold rounded-xl transition">Clear</a>
        </div>
    </form>

    {{-- Bookings Table --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-800/50 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                    <th class="px-6 py-4">Booking Ref</th>
                    <th class="px-6 py-4">Vehicle</th>
                    <th class="px-6 py-4 hidden sm:table-cell">Period</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Amount</th>
                    <th class="px-6 py-4 text-right">Invoice</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($rentals as $rental)
                <tr class="hover:bg-slate-800/30 transition">
                    <td class="px-6 py-4 text-xs font-black text-white">{{ $rental->rental_number ?? 'RNT-' . $rental->id }}</td>
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-white">{{ $rental->vehicle?->name ?? 'N/A' }}</p>
                        <p class="text-[10px] text-slate-500">{{ $rental->vehicle?->plate_number ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400 hidden sm:table-cell">
                        {{ optional($rental->start_date)->format('d M y') }} – {{ optional($rental->end_date)->format('d M y') ?? 'Ongoing' }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColor = match($rental->status) {
                                'active', 'dispatched' => 'emerald',
                                'completed', 'closed' => 'blue',
                                'overdue' => 'red',
                                'cancelled' => 'slate',
                                default => 'yellow',
                            };
                        @endphp
                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-{{ $statusColor }}-500/10 text-{{ $statusColor }}-400 border border-{{ $statusColor }}-500/20">
                            {{ $rental->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-black text-white text-right">
                        AED {{ number_format($rental->final_amount ?? 0, 0) }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($rental->payment_status === 'paid')
                            <a href="{{ route('portal.invoices.download', $rental) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-700 hover:bg-blue-600 text-xs font-bold text-white rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                                PDF
                            </a>
                        @else
                            <span class="text-xs text-slate-600">Not paid</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <p class="text-sm font-bold text-slate-500">No bookings found</p>
                        <p class="text-xs text-slate-600 mt-1">Try adjusting your filters</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($rentals->hasPages())
    <div class="mt-4">
        {{ $rentals->links() }}
    </div>
    @endif

</div>
@endsection
