@extends('layouts.customer')

@section('title', 'My Dashboard')
@section('header_title', 'My Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-700 rounded-3xl p-8 relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-56 h-56 bg-white/5 rounded-full"></div>
        <div class="absolute -right-4 bottom-0 w-32 h-32 bg-white/5 rounded-full"></div>
        <div class="relative z-10">
            <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-1">Welcome Back</p>
            <h2 class="text-3xl font-black text-white mb-2">{{ auth()->user()->name }}</h2>
            <p class="text-blue-200 text-sm">Your fleet rental hub — track bookings, download invoices, and monitor your trips.</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Loyalty Points</p>
            <p class="text-3xl font-black text-yellow-400 mt-2">{{ number_format($loyaltyPoints ?? 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">1 pt per AED 10 spent</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Total Spent</p>
            <p class="text-3xl font-black text-white mt-2">AED {{ number_format($totalSpent ?? 0, 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Lifetime value</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Active Bookings</p>
            <p class="text-3xl font-black text-blue-400 mt-2">{{ count($activeBookings ?? []) }}</p>
            <p class="text-xs text-slate-500 mt-1">Currently in service</p>
        </div>
    </div>

    {{-- Active Bookings --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-black text-white uppercase tracking-widest">Active Bookings</h3>
            <a href="{{ route('portal.bookings.index') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 uppercase tracking-widest transition">View All →</a>
        </div>

        @forelse($activeBookings ?? [] as $rental)
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-white">{{ $rental->vehicle?->name ?? 'Vehicle Pending' }}</p>
                    <p class="text-xs text-slate-400">{{ $rental->vehicle?->plate_number ?? 'N/A' }} · {{ ucfirst($rental->rental_type) }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ optional($rental->start_date)->format('d M Y') }} — {{ optional($rental->end_date)->format('d M Y') ?? 'Ongoing' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                    {{ in_array($rental->status, ['active', 'dispatched']) ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' : 'bg-blue-500/15 text-blue-400 border border-blue-500/20' }}">
                    {{ $rental->status }}
                </span>
                <span class="text-sm font-black text-white">AED {{ number_format($rental->final_amount ?? $rental->grand_total ?? 0, 0) }}</span>
            </div>
        </div>
        @empty
        <div class="bg-slate-900 border border-dashed border-slate-700 rounded-2xl p-10 text-center">
            <p class="text-sm font-bold text-slate-500">No active bookings at the moment</p>
            <p class="text-xs text-slate-600 mt-1">Contact your fleet manager to create a new booking</p>
        </div>
        @endforelse
    </div>

    {{-- Recent History --}}
    @if(isset($recentRentals) && $recentRentals->count())
    <div>
        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-4">Recent History</h3>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/50 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-3">Booking</th>
                        <th class="px-6 py-3">Vehicle</th>
                        <th class="px-6 py-3">Duration</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3 text-right">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @foreach($recentRentals as $rental)
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="px-6 py-4 text-xs font-black text-white">{{ $rental->rental_number ?? 'RNT-' . $rental->id }}</td>
                        <td class="px-6 py-4 text-xs text-slate-300">{{ $rental->vehicle?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-xs text-slate-400">{{ optional($rental->start_date)->format('d M') }} – {{ optional($rental->end_date)->format('d M y') }}</td>
                        <td class="px-6 py-4 text-sm font-black text-white text-right">AED {{ number_format($rental->final_amount ?? 0, 0) }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($rental->payment_status === 'paid')
                            <a href="{{ route('portal.invoices.download', $rental) }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 transition">↓ Download</a>
                            @else
                            <span class="text-xs text-slate-600">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
