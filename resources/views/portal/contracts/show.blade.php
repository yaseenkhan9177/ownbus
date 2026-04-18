@extends('layouts.company')

@section('title', 'Tactical Analysis - Contract ' . $contract->contract_number)

@section('header_title')
<div class="flex items-center space-x-3">
    <div class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-xl">
        <i class="bi bi-file-earmark-lock2-fill"></i>
    </div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Contract Analysis: {{ $contract->contract_number }}</h1>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto pb-20 space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">

    {{-- Status Ribbon --}}
    <div class="flex items-center justify-between bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm">
        <div class="flex items-center space-x-6">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Current State</p>
                @php
                $statusColors = [
                'draft' => 'bg-slate-100 text-slate-600',
                'active' => 'bg-emerald-100 text-emerald-600',
                'expired' => 'bg-amber-100 text-amber-600',
                'terminated' => 'bg-rose-100 text-rose-600',
                'completed' => 'bg-cyan-100 text-cyan-600',
                ];
                $color = $statusColors[$contract->status] ?? 'bg-slate-100 text-slate-600';
                @endphp
                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $color }}">
                    {{ $contract->status }}
                </span>
            </div>
            <div class="h-10 w-px bg-slate-100 dark:bg-slate-800"></div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Contract Lifecycle</p>
                <p class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                    {{ $contract->start_date->format('M d, Y') }} — {{ $contract->end_date->format('M d, Y') }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            @if($contract->status === 'draft')
            <form action="{{ route('company.contracts.activate', $contract) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                    Authorize Deployment
                </button>
            </form>
            @endif

            @if($contract->status === 'active')
            <button onclick="document.getElementById('terminate-modal').classList.remove('hidden')" class="px-6 py-2.5 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Emergency Termination
            </button>
            @endif

            <a href="{{ route('company.contracts.download', $contract) }}" class="px-6 py-2.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center">
                <i class="bi bi-file-earmark-pdf-fill mr-2 text-sm"></i> Download PDF
            </a>

            <button onclick="window.print()" type="button" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all d-print-none flex items-center">
                <i class="bi bi-printer-fill mr-2 text-sm"></i> Print Matrix
            </button>

            <a href="{{ route('company.contracts.edit', $contract) }}" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Edit Matrix
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Primary Intel (2/3) --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Entity & Asset Matrix --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center space-x-2">
                    <div class="w-1.5 h-4 bg-cyan-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Asset & Entity Mapping</h2>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        {{-- Client --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Target Client</label>
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-person-badge-fill text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 dark:text-white">{{ $contract->customer->company_name ?: $contract->customer->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">{{ $contract->customer->customer_code }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Asset --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Deployed Asset</label>
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-bus-front-fill text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 dark:text-white">{{ $contract->vehicle->vehicle_number }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">{{ $contract->vehicle->model }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Agent --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Field Agent</label>
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-person-workspace text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 dark:text-white">{{ $contract->driver ? $contract->driver->name : 'N/A' }}</p>
                                    @if($contract->driver)
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">License: {{ $contract->driver->license_number }}</p>
                                    @else
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">Self-Drive Policy</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Financial & Temporal Intel --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Temporal Matrix --}}
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center space-x-2">
                        <div class="w-1.5 h-4 bg-amber-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Temporal Matrix</h2>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Operational Start</span>
                            <div class="text-right">
                                <p class="text-sm font-black text-slate-900 dark:text-white uppercase">{{ $contract->start_date->format('d M Y') }}</p>
                                <p class="text-[10px] font-bold text-amber-500 uppercase">{{ $contract->start_time ?: '00:00' }} HRS</p>
                            </div>
                        </div>
                        <div class="h-px bg-slate-50 dark:bg-slate-800"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Operational End</span>
                            <div class="text-right">
                                <p class="text-sm font-black text-slate-900 dark:text-white uppercase">{{ $contract->end_date->format('d M Y') }}</p>
                                <p class="text-[10px] font-bold text-amber-500 uppercase">{{ $contract->end_time ?: '23:59' }} HRS</p>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-amber-50/50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1 text-center">Duration Analysis</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white text-center">
                                {{ $contract->start_date->diffInDays($contract->end_date) }} <span class="text-[10px] uppercase text-slate-400">Total Deployment Days</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Fiscal Matrix --}}
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center space-x-2">
                        <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Fiscal Matrix</h2>
                    </div>
                    <div class="p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Asset Value</span>
                            <p class="text-sm font-black text-slate-900 dark:text-white uppercase">{{ number_format($contract->contract_value, 2) }} AED</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Monthly Installment</span>
                            <p class="text-sm font-black text-slate-900 dark:text-white uppercase">{{ number_format($contract->monthly_rate, 2) }} AED</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Extra Surcharges</span>
                            <p class="text-sm font-black text-rose-500 uppercase">+ {{ number_format($contract->extra_charges, 2) }} AED</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tactical Discount</span>
                            <p class="text-sm font-black text-emerald-500 uppercase">- {{ number_format($contract->discount, 2) }} AED</p>
                        </div>
                        <div class="h-px bg-slate-50 dark:bg-slate-800 my-2"></div>
                        <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-900/10 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-900/30">
                            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Net Payable</span>
                            <p class="text-lg font-black text-slate-900 dark:text-white uppercase">
                                {{ number_format($contract->contract_value + $contract->extra_charges - $contract->discount, 2) }} AED
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terms & Intelligence --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center space-x-2">
                    <div class="w-1.5 h-4 bg-purple-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Compliance & Legal Intel</h2>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">Standard Policy Framework</label>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-400">
                            {!! nl2br(e($contract->terms ?: 'No specific terms specified for this engagement.')) !!}
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">Payment Architecture</label>
                        <div class="p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <p class="text-xs font-bold text-slate-900 dark:text-white italic">
                                "{{ $contract->payment_terms ?: 'Standard billing via commercial invoice.' }}"
                            </p>
                            @if($contract->payment_due_date)
                            <div class="mt-4 flex items-center space-x-2 text-rose-500">
                                <i class="bi bi-clock-history"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Settlement Deadline: {{ $contract->payment_due_date->format('d M Y') }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Internal Intel --}}
                        <div class="mt-8">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">Tactical Notes (Internal)</label>
                            <div class="p-5 bg-slate-900 rounded-2xl border border-white/5">
                                <p class="text-[11px] font-bold text-cyan-400 leading-relaxed italic">
                                    {{ $contract->notes ?: 'Zero external operational anomalies noted.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tactical Side Panel (1/3) --}}
        <div class="space-y-8">

            {{-- Billing Snapshot --}}
            <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-6">Billing Intelligence</h3>

                <div class="space-y-6">
                    <div>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Cycle Protocol</p>
                        <p class="text-sm font-black uppercase text-white">{{ $contract->billing_cycle }} Engine</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Last Iteration</p>
                            <p class="text-sm font-black text-white italic">{{ $contract->last_billed_at ? $contract->last_billed_at->format('d M Y') : 'Never' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Next Sequence</p>
                            <p class="text-sm font-black text-indigo-400 italic">{{ $contract->next_billing_date ? $contract->next_billing_date->format('d M Y') : 'Pending Authorize' }}</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/10">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Auto-Renew Protocol</span>
                            <span class="text-[10px] font-black uppercase {{ $contract->auto_renew ? 'text-emerald-400' : 'text-slate-400' }}">
                                {{ $contract->auto_renew ? 'ENABLED' : 'TERMINAL' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Document Vault --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Document Vault</h2>
                    </div>
                    <span class="text-[9px] font-black text-indigo-600 bg-indigo-100 px-2 py-1 rounded-lg">{{ $contract->documents->count() }} Assets</span>
                </div>
                <div class="p-6">
                    @forelse($contract->documents as $doc)
                    <div class="group flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl mb-3 border border-transparent hover:border-indigo-400 transition-all">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white dark:bg-slate-900 rounded-lg flex items-center justify-center text-indigo-500 shadow-sm">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </div>
                            <div class="max-w-[120px]">
                                <p class="text-[11px] font-black text-slate-900 dark:text-white truncate" title="{{ $doc->file_name }}">{{ $doc->file_name }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $doc->document_type }}</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="w-8 h-8 bg-white dark:bg-slate-900 text-slate-400 hover:text-indigo-500 rounded-lg flex items-center justify-center shadow-sm transition-colors">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="bi bi-folder2-open text-xl"></i>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Zero Tactical Assets</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Event Timeline --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden p-8">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6">Execution Log</h3>
                <div class="space-y-6 relative before:absolute before:inset-y-0 before:left-3 before:w-0.5 before:bg-slate-50 dark:before:bg-slate-800">
                    <div class="relative pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-500/10 -translate-x-1/2"></div>
                        <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest leading-none mb-1">Initialization</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $contract->created_at->format('M d, H:i') }} HRS</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Terminate Modal --}}
<div id="terminate-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="document.getElementById('terminate-modal').classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-8">
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-slate-800">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/30 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Emergency Termination</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed mb-8">This will immediately halt the contract billing sequence and flag the asset for recovery. Please provide a formal justification.</p>

                <form action="{{ route('company.contracts.terminate', $contract) }}" method="POST" class="space-y-6">
                    @csrf
                    <textarea name="reason" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none transition-all resize-none" placeholder="REASON FOR TERMINATION..." rows="3"></textarea>

                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="document.getElementById('terminate-modal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-500/20">
                            Execute Halt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection