@extends('layouts.company')

@section('title', 'Vendor Intelligence - ' . $vendor->name)

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Vendor Intelligence</h1>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-500">

    {{-- 1. Profile Header --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-3xl font-black border border-blue-200 dark:border-blue-800 shadow-sm">
                        {{ substr($vendor->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $vendor->name }}</h2>
                        <div class="flex items-center space-x-4 mt-1">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">ID: {{ $vendor->vendor_code }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $vendor->city ?? 'Location Unknown' }}</span>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            @php
                            $isSuspended = $vendor->isSuspended();
                            $color = $isSuspended ? 'rose' : 'emerald';
                            @endphp
                            <div class="inline-flex items-center px-3 py-1 rounded-lg bg-{{ $color }}-500/10 text-{{ $color }}-500 border border-current/10">
                                <div class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500 mr-2 {{ !$isSuspended ? 'animate-pulse' : '' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $isSuspended ? 'Suspended' : 'Operational' }}</span>
                            </div>

                            <form action="{{ route('company.vendors.suspend', $vendor) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-rose-500 transition-colors">
                                    {{ $isSuspended ? 'Reactivate Vendor' : 'Suspend Account' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('company.vendors.edit', $vendor) }}" class="p-2.5 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors border border-gray-100 dark:border-slate-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mt-10 border-t border-gray-50 dark:border-slate-800 pt-6">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">TRN / Tax Number</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $vendor->tax_number ?? 'Not Filed' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Contact Phone</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $vendor->phone ?? 'Unlisted' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Direct Email</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $vendor->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Deployment Branch</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $vendor->branch->name ?? 'Company-Wide' }}</p>
                </div>
            </div>
        </div>

        {{-- 2. Financial Snapshot --}}
        <div class="bg-slate-900 dark:bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-2xl flex flex-col justify-between">
            <div>
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Live AP Balance</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-4xl font-black text-white">AED {{ number_format($outstanding, 2) }}</span>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-1 italic">
                    {{ $outstanding > 0 ? "PAYABLE TO VENDOR" : ($outstanding < 0 ? "RECEIVABLE FROM VENDOR" : "LEGER BALANCED") }}
                </p>
            </div>

            <div class="space-y-3 mt-6">
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-500">
                    <span>Opening Balance</span>
                    <span class="text-slate-300">AED {{ number_format($vendor->opening_balance, 2) }}</span>
                </div>
                <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full" style="width: 100%"></div>
                </div>
                <p class="text-[9px] text-slate-500 text-right font-bold tracking-tighter uppercase italic">Account created {{ $vendor->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- 3. Detailed Tabs / Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Billing History --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Recent Invoices / Bills</h3>
                    <a href="{{ route('company.vendor-bills.create', ['vendor_id' => $vendor->id]) }}" class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest hover:underline">+ New Bill</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                                <th class="py-3 px-6">Bill #</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-6 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                            @forelse($vendor->bills as $bill)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-6">
                                    <a href="{{ route('company.vendor-bills.show', $bill) }}" class="text-xs font-black text-slate-900 dark:text-white hover:text-blue-500 transition-colors">
                                        {{ $bill->bill_number }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-xs font-bold text-slate-600 dark:text-slate-400">
                                    {{ $bill->bill_date->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-{{ $bill->statusColor() }}-500/10 text-{{ $bill->statusColor() }}-500 text-[9px] font-black uppercase tracking-tighter border border-current/10">
                                        {{ $bill->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-right text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter">
                                    AED {{ number_format($bill->total_amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">No bill history found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Maintenance History (If redirected from maintenance) --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Service Artifacts (Maintenance)</h3>
                </div>
                <div class="p-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    Maintenance module integration pending records
                </div>
            </div>
        </div>

        {{-- Right: Technical Details --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6">Logistics Intel</h3>

                <div class="space-y-6">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Company Address</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 leading-relaxed">{{ $vendor->address ?? 'No physical address tracked' }}</p>
                    </div>

                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">System Creator</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-black text-slate-500">
                                {{ substr($vendor->creator->name ?? '?', 0, 1) }}
                            </div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $vendor->creator->name ?? 'System' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-rose-500/5 border border-rose-500/20 rounded-2xl p-6">
                <h3 class="text-xs font-black text-rose-500 uppercase tracking-widest mb-2">Liquidate Record</h3>
                <p class="text-[10px] text-rose-500/60 font-medium mb-4 italic">Deleting a vendor is only possible if they have no registered invoices or transactions in the ledger.</p>

                <form action="{{ route('company.vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('CRITICAL: This will permanently remove vendor metadata. Continue?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2.5 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Execute Deletion
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection