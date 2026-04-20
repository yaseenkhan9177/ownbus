@extends('portal.layout')

@section('title', 'Fines Analysis Report | OwnBus')

@section('content')
<div class="px-6 py-8">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Fines Analysis Report</h1>
            <p class="text-slate-500 text-sm">Detailed breakdown of violations and financial impact</p>
        </div>
        <button onclick="window.print()" class="bg-white border border-slate-200 px-5 py-2.5 rounded-xl text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z" /></svg>
            Print Report
        </button>
    </div>

    <!-- Filters Row -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8 no-print">
        <form action="{{ route('company.fines.report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Vehicles</label>
                <select name="vehicle_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500">
                    <option value="">All Vehicles</option>
                    @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->vehicle_number }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Drivers</label>
                <select name="driver_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500">
                    <option value="">All Drivers</option>
                    @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">Filter Results</button>
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <div class="bg-slate-800 text-white p-8 rounded-3xl shadow-xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Fine Value</p>
            <h3 class="text-3xl font-black">AED {{ number_format($fines->sum('amount'), 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-tight">Period: Current Portfolio</p>
        </div>
        <div class="bg-indigo-900 text-white p-8 rounded-3xl shadow-xl">
            <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest mb-2">Liability Recovery</p>
            <h3 class="text-3xl font-black">AED {{ number_format($fines->where('customer_responsible', true)->sum('amount'), 2) }}</h3>
            <p class="text-[10px] text-indigo-300 mt-2 uppercase tracking-tight">Allocated for Recovery</p>
        </div>
        <div class="bg-emerald-600 text-white p-8 rounded-3xl shadow-xl">
            <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest mb-2">Settled Fines</p>
            <h3 class="text-3xl font-black">AED {{ number_format($fines->where('status', 'paid')->sum('amount'), 2) }}</h3>
            <p class="text-[10px] text-emerald-100 mt-2 uppercase tracking-tight">{{ $fines->where('status', 'paid')->count() }} COMPLETED</p>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-left">
                    <th class="px-6 py-4 text-xs font-black text-slate-800 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-800 uppercase tracking-wider">Fine #</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-800 uppercase tracking-wider">Vehicle</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-800 uppercase tracking-wider">Driver</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-800 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-800 uppercase tracking-wider">Type</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($fines as $fine)
                <tr class="text-xs">
                    <td class="px-6 py-4 font-medium">{{ $fine->fine_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 font-black uppercase">{{ $fine->fine_number }}</td>
                    <td class="px-6 py-4 font-bold text-indigo-600">{{ $fine->vehicle->vehicle_number }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $fine->driver->name ?? '-' }}</td>
                    <td class="px-6 py-4 font-black">AED {{ number_format($fine->amount, 2) }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $fine->fine_type }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style media="print">
    .no-print { display: none !important; }
    body { background: white !important; }
    .shadow-xl, .shadow-sm { box-shadow: none !important; }
    .rounded-3xl, .rounded-2xl { border-radius: 0 !important; }
</style>
@endpush
