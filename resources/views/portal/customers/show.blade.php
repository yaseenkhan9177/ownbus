@extends('layouts.company')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('company.customers.index') }}" class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">{{ $customer->name }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $customer->customer_code }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $customer->type }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('company.customers.edit', $customer) }}" class="bg-slate-800 hover:bg-slate-700 text-white px-6 py-3 rounded-xl font-bold transition">
                Edit Profile
            </a>
            <a href="{{ route('company.rentals.create', ['customer_id' => $customer->id]) }}" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shadow-blue-900/20">
                New Rental
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl text-emerald-400 font-bold flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column: Core Info -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Profile Card -->
            <div class="bg-[#0f172a] border border-slate-800 rounded-3xl overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700 relative">
                    <div class="absolute -bottom-10 left-8">
                        <div class="w-20 h-20 rounded-2xl bg-slate-900 border-4 border-slate-900 flex items-center justify-center text-3xl font-black text-blue-500 shadow-xl">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="absolute bottom-4 right-6">
                        @if($customer->status === 'active')
                        <span class="bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-500/30">Active</span>
                        @elseif($customer->status === 'blacklisted')
                        <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-500/30">Blacklisted</span>
                        @else
                        <span class="bg-slate-500/20 text-slate-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-500/30">{{ $customer->status }}</span>
                        @endif
                    </div>
                </div>
                <div class="pt-14 p-8 space-y-6">
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Contact Details</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="p-2 rounded-lg bg-slate-800 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg></span>
                                <span class="text-sm font-medium text-white">{{ $customer->email ?: 'No email provided' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="p-2 rounded-lg bg-slate-800 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg></span>
                                <span class="text-sm font-medium text-white">{{ $customer->phone }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="p-2 rounded-lg bg-slate-800 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg></span>
                                <span class="text-sm font-medium text-white">{{ $customer->city ?: 'UAE' }}, {{ $customer->country }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-800">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Identity Verification</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 rounded-2xl bg-slate-900 border border-slate-800">
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">National ID</p>
                                <p class="text-xs font-bold text-white">{{ $customer->national_id ?: 'Pending' }}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-900 border border-slate-800">
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Driving License</p>
                                <p class="text-xs font-bold text-white">{{ $customer->driving_license_no ?: 'Pending' }}</p>
                            </div>
                        </div>
                        @if($customer->driving_license_expiry)
                        <div class="mt-4 p-3 rounded-2xl bg-amber-500/5 border border-amber-500/20">
                            <p class="text-[10px] text-amber-500 font-black uppercase tracking-widest mb-1">License Expiry</p>
                            <p class="text-xs font-bold text-slate-300">{{ $customer->driving_license_expiry->format('d M, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Documents Card -->
            <div class="bg-[#0f172a] border border-slate-800 rounded-3xl p-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">Uploaded Documents</h3>
                <div class="space-y-4">
                    @forelse($customer->documents as $doc)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900 hover:bg-slate-800 transition border border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-blue-500/10 flex items-center justify-center text-blue-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">{{ $doc->document_type }}</p>
                                <p class="text-[10px] text-slate-500">{{ $doc->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <a href="#" class="text-slate-500 hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-6 border-2 border-dashed border-slate-800 rounded-2xl">
                        <p class="text-xs font-medium text-slate-600">No documents uploaded</p>
                    </div>
                    @endforelse

                    <button class="w-full mt-2 py-3 border border-slate-700 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-slate-800 transition border-dashed">
                        + Upload Document Scan
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Financials & History -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Financial Performance KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#0f172a] border border-slate-800 p-6 rounded-3xl relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-600/5 rounded-full group-hover:scale-150 transition-all duration-700"></div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Lifetime Revenue</p>
                    <p class="text-2xl font-black text-white mt-2">AED {{ number_format($metrics['lifetime_revenue'], 2) }}</p>
                    <p class="text-[10px] font-bold text-blue-500 mt-1 uppercase">Top 10% Contributor</p>
                </div>
                <div class="bg-[#0f172a] border border-slate-800 p-6 rounded-3xl relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-600/5 rounded-full group-hover:scale-150 transition-all duration-700"></div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Current Balance</p>
                    <p class="text-2xl font-black {{ $customer->risk_level === 'green' ? 'text-white' : 'text-amber-500' }} mt-2">AED {{ number_format($customer->current_balance, 2) }}</p>
                    <div class="mt-2 w-full h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full {{ $customer->risk_level === 'yellow' ? 'bg-amber-500' : 'bg-blue-600' }}" style="width: {{ $customer->credit_limit > 0 ? min(100, ($customer->current_balance / $customer->credit_limit) * 100) : 0 }}%"></div>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 mt-1 uppercase text-right">Limit: AED {{ number_format($customer->credit_limit, 2) }}</p>
                </div>
                <div class="bg-[#0f172a] border border-slate-800 p-6 rounded-3xl relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-600/5 rounded-full group-hover:scale-150 transition-all duration-700"></div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Avg Rental Value</p>
                    <p class="text-2xl font-black text-emerald-500 mt-2">AED {{ number_format($metrics['average_rental_value'], 2) }}</p>
                    <p class="text-[10px] font-bold text-slate-500 mt-1 uppercase">Per booking efficiency</p>
                </div>
            </div>

            <!-- Activity KPIs -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-[#0f172a]/50 border border-slate-800 p-4 rounded-2xl flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Rentals</p>
                        <p class="text-lg font-black text-white leading-none">{{ $metrics['total_rentals'] }}</p>
                    </div>
                </div>
                <div class="bg-[#0f172a]/50 border border-slate-800 p-4 rounded-2xl flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Active Now</p>
                        <p class="text-lg font-black text-white leading-none">{{ $metrics['active_rentals'] }}</p>
                    </div>
                </div>
                <div class="bg-[#0f172a]/50 border border-slate-800 p-4 rounded-2xl flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Overdue</p>
                        <p class="text-lg font-black text-white leading-none">{{ $metrics['overdue_rentals_count'] }}</p>
                    </div>
                </div>
                <div class="bg-[#0f172a]/50 border border-slate-800 p-4 rounded-2xl flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Last Activity</p>
                        <p class="text-xs font-bold text-white leading-none uppercase">{{ $metrics['last_rental_date'] ? $metrics['last_rental_date']->format('d M y') : 'Never' }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Rentals Table -->
            <div class="bg-[#0f172a] border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                <div class="px-8 py-6 border-b border-slate-800 bg-slate-900/30 flex justify-between items-center">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Recent Rental History</h3>
                    <a href="#" class="text-blue-500 hover:text-blue-400 text-xs font-bold uppercase tracking-widest transition">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-900/50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Contract #</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Vehicle</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Duration</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Amount</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($customer->rentals as $rental)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-8 py-4 text-xs font-black text-white">{{ $rental->rental_number ?: $rental->contract_number }}</td>
                                <td class="px-8 py-4">
                                    <p class="text-xs font-bold text-white">{{ $rental->vehicle ? $rental->vehicle->name : 'Unassigned' }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $rental->vehicle ? $rental->vehicle->plate_number : 'N/A' }}</p>
                                </td>
                                <td class="px-8 py-4">
                                    <p class="text-xs font-medium text-slate-300">{{ $rental->start_date->format('M d') }} - {{ $rental->end_date ? $rental->end_date->format('M d') : 'Open' }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase">{{ $rental->rental_type }}</p>
                                </td>
                                <td class="px-8 py-4 text-sm font-black text-white">AED {{ number_format($rental->final_amount ?: $rental->grand_total, 2) }}</td>
                                <td class="px-8 py-4">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider 
                                        {{ in_array($rental->status, ['active', 'confirmed']) ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20' }}">
                                        {{ $rental->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center">
                                    <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">No rentals recorded for this profile</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ledger Statement Table -->
            <div class="bg-[#0f172a] border border-slate-800 rounded-3xl overflow-hidden shadow-xl mt-8">
                <div class="px-8 py-6 border-b border-slate-800 bg-slate-900/30 flex justify-between items-center">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">A/R Ledger Statement</h3>
                    <a href="#" class="text-blue-500 hover:text-blue-400 text-xs font-bold uppercase tracking-widest transition">Print PDF</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-900/50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Date</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Reference</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Description</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Debit (AED)</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Credit (AED)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($ledgerLines ?? [] as $line)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-8 py-4 text-xs font-medium text-slate-300">{{ $line->created_at->format('d M y, H:i') }}</td>
                                <td class="px-8 py-4 text-xs font-black text-white">JE-{{ str_pad($line->journal_entry_id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-8 py-4 text-xs font-medium text-slate-400 max-w-[250px] truncate" title="{{ $line->journalEntry->description }}">
                                    {{ $line->description ?: $line->journalEntry->description }}
                                </td>
                                <td class="px-8 py-4 text-sm font-black text-red-400 text-right">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                                <td class="px-8 py-4 text-sm font-black text-emerald-400 text-right">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center">
                                    <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">No financial transactions recorded for this profile</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes & Audit Trail Meta -->
            <div class="bg-[#0f172a] border border-slate-800 rounded-3xl p-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-500 font-bold uppercase text-[10px]">Audit Ready</span>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Profile Insights & Notes</h3>
                </div>
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 text-sm text-slate-300 leading-relaxed italic">
                    {{ $customer->notes ?: 'No manager notes available for this customer.' }}
                </div>
                <div class="mt-6 flex flex-wrap gap-x-8 gap-y-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest">
                    <span>Added By: {{ $customer->creator ? $customer->creator->name : 'System' }}</span>
                    <span>Created: {{ $customer->created_at->format('M d, Y H:i') }}</span>
                    <span>Last Updated: {{ $customer->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection