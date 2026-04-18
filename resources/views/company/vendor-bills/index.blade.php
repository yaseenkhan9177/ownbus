@extends('layouts.company')

@section('title', 'Vendor Bills - AP Ledger')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-slate-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Vendor Bills (Payables)</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500">

    {{-- 1. Toolbar & Stats --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total Payables</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $bills->total() }}</p>
            </div>
            <div class="p-3 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">Awaiting Approval</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $bills->where('status', 'draft')->count() }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('company.reports.export.vendor-bills.pdf', request()->all()) }}" class="p-2.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-200 transition-colors tooltip" title="Export PDF">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </a>
            <a href="{{ route('company.reports.export.vendor-bills.excel', request()->all()) }}" class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl hover:bg-emerald-200 transition-colors tooltip" title="Export Excel">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </a>
            <button onclick="window.print()" type="button" class="p-2.5 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400 rounded-xl hover:bg-gray-200 transition-colors tooltip" title="Print List">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
            </button>
            <a href="{{ route('company.vendor-bills.create') }}" class="px-4 py-2.5 bg-slate-900 dark:bg-slate-800 text-white hover:bg-slate-800 dark:hover:bg-slate-700 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg transition-all flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Generate Bill
            </a>
        </div>
    </div>

    {{-- 2. Advanced Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
        <form action="{{ route('company.vendor-bills.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">

            <select name="vendor_id" class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500" onchange="this.form.submit()">
                <option value="">All Vendors</option>
                @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                @endforeach
            </select>

            <select name="status" class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500"
                placeholder="From Date">

            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500"
                placeholder="To Date">

            <div class="flex items-center space-x-2 lg:col-span-2">
                <button type="submit" class="flex-1 py-2.5 bg-slate-900 dark:bg-slate-800 text-white rounded-xl hover:bg-slate-800 transition-colors text-xs font-black uppercase tracking-widest">
                    Apply Intelligence Filter
                </button>
                <a href="{{ route('company.vendor-bills.index') }}" class="px-4 py-2.5 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 dark:hover:text-white transition-colors">Reset</a>
            </div>
        </form>
    </div>

    {{-- 3. Bills Table --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                        <th class="py-4 px-6">Bill Artifact</th>
                        <th class="py-4 px-4">Vendor Relationship</th>
                        <th class="py-4 px-4">Date Payload</th>
                        <th class="py-4 px-4">Status Vector</th>
                        <th class="py-4 px-4 text-right">Total Payable</th>
                        <th class="py-4 px-6 text-right">Command</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @forelse($bills as $bill)
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <a href="{{ route('company.vendor-bills.show', $bill) }}" class="text-sm font-black text-slate-900 dark:text-white group-hover:text-blue-500 transition-colors uppercase tracking-tight">
                                    #{{ $bill->bill_number }}
                                </a>
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Added by {{ $bill->creator->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-xs font-bold text-slate-700 dark:text-slate-300">
                            {{ $bill->vendor->name }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-tighter">{{ $bill->bill_date->format('d M Y') }}</span>
                                <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Due: {{ $bill->due_date ? $bill->due_date->format('d M Y') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="inline-flex items-center px-1.5 py-1 rounded-lg border border-current/10 bg-{{ $bill->statusColor() }}-500 bg-opacity-10 text-{{ $bill->statusColor() }}-500">
                                <div class="w-1.5 h-1.5 rounded-full bg-{{ $bill->statusColor() }}-500 mr-2"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $bill->status }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter">
                                AED {{ number_format($bill->total_amount, 2) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('company.vendor-bills.show', $bill) }}" class="p-2.5 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors border border-gray-100 dark:border-slate-800 text-[10px] font-black uppercase tracking-widest">
                                Open Record
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            No account payables discovered
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bills->hasPages())
        <div class="px-6 py-4 border-t border-gray-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection