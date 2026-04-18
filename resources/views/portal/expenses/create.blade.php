@extends('layouts.company')

@section('title', 'New Expense — Create')

@section('header_title')
<div class="flex items-center gap-3">
    <a href="{{ route('company.expenses.index') }}" class="flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>
    <div>
        <h1 class="text-base font-black text-slate-900 dark:text-white tracking-tight uppercase leading-none">Record Expense</h1>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Financial Operations</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto" x-data="expenseForm()">

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="mb-6 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-2">Please fix the following errors</p>
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-[11px] text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1.5">
                        <span class="w-1 h-1 bg-rose-400 rounded-full"></span>{{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('company.expenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

            {{-- ===== MAIN FORM COLUMN ===== --}}
            <div class="xl:col-span-2 space-y-5">

                {{-- Section 1: Basic Info --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">General Information</h2>
                            <p class="text-[9px] text-slate-400 font-medium uppercase tracking-widest mt-0.5">Category, Branch & Date</p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Branch --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Branch <span class="text-rose-400">*</span></label>
                            <select name="branch_id" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all appearance-none">
                                <option value="">Select Branch...</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Category --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Category <span class="text-rose-400">*</span></label>
                            <select name="category" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all appearance-none">
                                <option value="">Select Category...</option>
                                <option value="fuel" {{ old('category') == 'fuel' ? 'selected' : '' }}>Fuel</option>
                                <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="salaries" {{ old('category') == 'salaries' ? 'selected' : '' }}>Salaries</option>
                                <option value="rent" {{ old('category') == 'rent' ? 'selected' : '' }}>Rent</option>
                                <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="utilities" {{ old('category') == 'utilities' ? 'selected' : '' }}>Utilities</option>
                                <option value="fines" {{ old('category') == 'fines' ? 'selected' : '' }}>Fines</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Expense Date <span class="text-rose-400">*</span></label>
                            <input type="date" name="expense_date" required value="{{ old('expense_date', date('Y-m-d')) }}"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all">
                        </div>

                        {{-- Vendor --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Vendor (Optional)</label>
                            <select name="vendor_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all appearance-none">
                                <option value="">Select Vendor...</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Vehicle --}}
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Assigned Vehicle (Optional)</label>
                            <select name="vehicle_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition-all appearance-none">
                                <option value="">Select Vehicle...</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->vehicle_number }} — {{ $vehicle->make }} {{ $vehicle->model }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Payment Details --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <div class="w-8 h-8 rounded-xl bg-violet-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Payment & Reference</h2>
                            <p class="text-[9px] text-slate-400 font-medium uppercase tracking-widest mt-0.5">Method & Invoice Upload</p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Payment Method --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Payment Method <span class="text-rose-400">*</span></label>
                            <select name="payment_method" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-violet-500 transition-all appearance-none">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer / Card</option>
                                <option value="payable" {{ old('payment_method') == 'payable' ? 'selected' : '' }}>Payable (Credit)</option>
                            </select>
                        </div>

                        {{-- Reference No --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reference / Receipt #</label>
                            <input type="text" name="reference_no" value="{{ old('reference_no') }}" placeholder="e.g. TXN-9982"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-violet-500 transition-all">
                        </div>

                        {{-- Invoice Upload --}}
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Upload Invoice / Proof</label>
                            <input type="file" name="invoice" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-500 focus:ring-2 focus:ring-violet-500 transition-all cursor-pointer">
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Description <span class="text-rose-400">*</span></label>
                            <textarea name="description" required rows="2" placeholder="Describe the expense details..."
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-violet-500 resize-none transition-all">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== SIDEBAR: FINANCIALS ===== --}}
            <div class="space-y-5 xl:sticky xl:top-6">

                <div class="bg-slate-900 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-2xl p-6 space-y-5">
                    <h3 class="text-[10px] font-black text-white/50 uppercase tracking-widest">Expense Breakdown</h3>

                    {{-- Amount Ex VAT --}}
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-white/40 uppercase tracking-widest">Amount (Excl. VAT)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm font-black">AED</span>
                            <input type="number" name="amount_ex_vat" x-model.number="amountExVat" step="0.01" required min="0"
                                class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/10 rounded-xl text-lg font-black text-white focus:ring-1 focus:ring-cyan-500 transition-all">
                        </div>
                    </div>

                    {{-- VAT Percentage --}}
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-white/40 uppercase tracking-widest">VAT Percentage (%)</label>
                        <select name="vat_percent" x-model.number="vatPercent" class="w-full px-4 py-3 bg-white/10 border border-white/10 rounded-xl text-sm font-black text-white focus:ring-1 focus:ring-cyan-500 transition-all appearance-none">
                            <option value="5" class="bg-slate-900">5% (Standard UAE)</option>
                            <option value="0" class="bg-slate-900">0% (Exempt)</option>
                        </select>
                    </div>

                    {{-- Summary Grid --}}
                    <div class="space-y-2.5 pt-4 border-t border-white/10">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Subtotal</span>
                            <span class="text-xs font-black text-white" x-text="fmt(amountExVat)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">VAT Amount</span>
                            <span class="text-xs font-black text-slate-400" x-text="fmt(vatAmount())"></span>
                            <input type="hidden" name="vat_amount" :value="vatAmount()">
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-white/10">
                            <span class="text-[10px] font-black text-cyan-400 uppercase tracking-widest">Total Amount</span>
                            <span class="text-xl font-black text-cyan-400" x-text="fmt(totalAmount())"></span>
                            <input type="hidden" name="total_amount" :value="totalAmount()">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-cyan-500 hover:bg-cyan-400 active:bg-cyan-600 text-slate-900 font-black text-xs uppercase tracking-widest rounded-xl transition-all shadow-xl shadow-cyan-500/20 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Record Expense
                    </button>
                </div>

                <div class="p-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                        Recording an expense will automatically generate the corresponding journal entries in the ledger.
                    </p>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    function expenseForm() {
        return {
            amountExVat: {
                {
                    old('amount_ex_vat', 0)
                }
            },
            vatPercent: {
                {
                    old('vat_percent', 5)
                }
            },

            vatAmount() {
                return (this.amountExVat * (this.vatPercent / 100)).toFixed(2);
            },

            totalAmount() {
                return (parseFloat(this.amountExVat) + parseFloat(this.vatAmount())).toFixed(2);
            },

            fmt(val) {
                return new Intl.NumberFormat('en-AE', {
                    style: 'currency',
                    currency: 'AED',
                    minimumFractionDigits: 2
                }).format(val);
            }
        }
    }
</script>
@endsection