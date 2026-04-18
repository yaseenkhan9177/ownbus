@extends('layouts.company')

@section('title', 'Generate Vendor Bill')

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-slate-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Generate Vendor Bill</h1>
</div>
@endsection

@section('content')
<div class="animate-in fade-in slide-in-from-bottom-2 duration-500"
    x-data="{ 
        items: {{ old('items') ? json_encode(old('items')) : '[{ expense_account_id: \'\', description: \'\', quantity: 1, unit_cost: 0 }]' }},
        tax: {{ old('tax_amount', 0) }},
        addItem() {
            this.items.push({ expense_account_id: '', description: '', quantity: 1, unit_cost: 0 });
        },
        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },
        calculateTotal() {
            let subtotal = this.items.reduce((total, item) => total + (item.quantity * item.unit_cost), 0);
            return (subtotal + Number(this.tax || 0)).toFixed(2);
        }
     }">

    @if($errors->any())
    <div class="mb-6 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800 rounded-2xl p-4 animate-shake">
        <div class="flex items-center mb-2">
            <svg class="w-4 h-4 text-rose-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xs font-black text-rose-600 uppercase tracking-widest">Initialization Faults Detected</h3>
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li class="text-[10px] font-bold text-rose-500 italic">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('company.vendor-bills.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- 1. Bill Payload --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center mr-3">01</span>
                Bill Headers & Origin
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Vendor / Supplier <span class="text-rose-500">*</span></label>
                    <select name="vendor_id" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500">
                        <option value="">Select Target Vendor...</option>
                        @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id', request('vendor_id')) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }} ({{ $vendor->vendor_code }})</option>
                        @endforeach
                    </select>
                    @error('vendor_id') <p class="text-rose-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Original Bill # <span class="text-rose-500">*</span></label>
                    <input type="text" name="bill_number" value="{{ old('bill_number') }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500"
                        placeholder="e.g. INV-2024-001">
                    @error('bill_number') <p class="text-rose-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deployment Branch</label>
                    <select name="branch_id"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500">
                        <option value="">Company-Wide</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Posting Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="bill_date" value="{{ old('bill_date', date('Y-m-d')) }}" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500">
                    @error('bill_date') <p class="text-rose-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Maturity Date (Due)</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tax Amount (VAT)</label>
                    <input type="number" step="0.01" name="tax_amount" x-model.number="tax"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500"
                        placeholder="0.00">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Strategic Description</label>
                    <input type="text" name="description" value="{{ old('description') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500"
                        placeholder="Internal notes about this liability...">
                </div>
            </div>
        </div>

        {{-- 2. Line Items (Dynamic Table) --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Expense Distribution Artifacts</h2>
                <button type="button" @click="addItem()" class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest hover:underline">+ Appending Row</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800">
                            <th class="py-4 px-6 w-1/4">Expense Account</th>
                            <th class="py-4 px-4 w-1/3">Artifact Description</th>
                            <th class="py-4 px-4 w-20">Qty</th>
                            <th class="py-4 px-4 w-32">Unit Cost</th>
                            <th class="py-4 px-4 text-right">Row Total</th>
                            <th class="py-4 px-6 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="group animate-in fade-in duration-300">
                                <td class="py-3 px-6">
                                    <select :name="`items[${index}][expense_account_id]`" required
                                        x-model="item.expense_account_id"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-3 py-2 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500">
                                        <option value="">Select Ledger Account...</option>
                                        @foreach($expenseAccounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-3 px-4">
                                    <input type="text" :name="`items[${index}][description]`" required
                                        x-model="item.description"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-3 py-2 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500"
                                        placeholder="Service or Product desc...">
                                </td>
                                <td class="py-3 px-4">
                                    <input type="number" step="0.01" :name="`items[${index}][quantity]`" required
                                        x-model="item.quantity"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-3 py-2 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 text-center">
                                </td>
                                <td class="py-3 px-4">
                                    <input type="number" step="0.01" :name="`items[${index}][unit_cost]`" required
                                        x-model="item.unit_cost"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-3 py-2 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-slate-500 text-right">

                                    {{-- Row level error feedback --}}
                                    <template x-if="$errors?.has(`items.${index}.expense_account_id`)">
                                        <p class="text-[8px] text-rose-500 mt-1 font-bold italic" x-text="$errors.first(`items.${index}.expense_account_id`)"></p>
                                    </template>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <span class="text-xs font-black text-slate-900 dark:text-white" x-text="`AED ${(item.quantity * item.unit_cost).toFixed(2)}`"></span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-rose-500 hover:text-rose-600 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-slate-50/50 dark:bg-slate-800/30 flex justify-end">
                <div class="w-full max-w-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Aggregate Total</span>
                        <span class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter" x-text="`AED ${calculateTotal()}`"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Actions --}}
        <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-slate-800">
            <a href="{{ route('company.vendor-bills.index') }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-white transition-colors">Abort Payload</a>

            <button type="submit" class="px-8 py-3 bg-slate-900 dark:bg-slate-800 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-xl transition-all hover:bg-slate-800">
                Initialize Draft Bill
            </button>
        </div>
    </form>
</div>
@endsection