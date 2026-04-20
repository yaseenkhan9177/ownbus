@extends('portal.layout')

@section('title', 'Create Invoice | OwnBus')

@section('content')
<div class="px-6 py-8" x-data="{ 
    items: [{ description: '', quantity: 1, unit_price: 0 }],
    addItem() {
        this.items.push({ description: '', quantity: 1, unit_price: 0 });
    },
    removeItem(index) {
        this.items.splice(index, 1);
    },
    get subtotal() {
        return this.items.reduce((acc, item) => acc + (item.quantity * item.unit_price), 0);
    },
    get vat() {
        return Math.round(this.subtotal * 0.05 * 100) / 100;
    },
    get total() {
        return this.subtotal + this.vat;
    }
}">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('company.invoices.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-slate-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Create New Invoice</h1>
            <p class="text-slate-500 text-sm">Issue a VAT compliant invoice to customer</p>
        </div>
    </div>

    <form action="{{ route('company.invoices.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Side: Basic Info -->
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-[0.1em] mb-6 border-b border-slate-100 pb-2">Billing Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Customer</label>
                            <select name="customer_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Related Rental (Optional)</label>
                            <select name="rental_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500">
                                <option value="">None</option>
                                @foreach($rentals as $rental)
                                <option value="{{ $rental->id }}">Rental #{{ $rental->id }} - {{ $rental->vehicle->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-[0.1em] mb-6 border-b border-slate-100 pb-2">Line Items</h3>
                    
                    <div class="space-y-4 mb-6">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="grid grid-cols-12 gap-4 items-end bg-slate-50/50 p-4 rounded-xl border border-dashed border-slate-200">
                                <div class="col-span-12 md:col-span-6">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Description</label>
                                    <input type="text" x-model="item.description" :name="`items[${index}][description]`" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Bus Rental Service" required>
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Qty</label>
                                    <input type="number" x-model.number="item.quantity" :name="`items[${index}][quantity]`" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" required>
                                </div>
                                <div class="col-span-5 md:col-span-3">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Unit Price (AED)</label>
                                    <input type="number" step="0.01" x-model.number="item.unit_price" :name="`items[${index}][unit_price]`" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" required>
                                </div>
                                <div class="col-span-3 md:col-span-1 flex justify-end">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addItem" class="text-indigo-600 text-xs font-bold uppercase tracking-wider flex items-center hover:text-indigo-700 transition-all">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Add Item
                    </button>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Notes / Terms</label>
                    <textarea name="notes" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Payment due within 7 days. Thank you for your business."></textarea>
                </div>
            </div>

            <!-- Right Side: Summary & Action -->
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-indigo-900 rounded-2xl p-8 shadow-xl text-white">
                    <h3 class="text-sm font-bold text-indigo-300 uppercase tracking-widest mb-6">Invoice Summary</h3>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-indigo-200 font-medium">Subtotal</span>
                            <span class="font-bold">AED <span x-text="subtotal.toLocaleString(undefined, {minimumFractionDigits: 2})"></span></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-indigo-200 font-medium">VAT (5%)</span>
                            <span class="font-bold">AED <span x-text="vat.toLocaleString(undefined, {minimumFractionDigits: 2})"></span></span>
                        </div>
                        <div class="h-px bg-indigo-800 w-full"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold">Total Amount</span>
                            <span class="text-2xl font-black">AED <span x-text="total.toLocaleString(undefined, {minimumFractionDigits: 2})"></span></span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider mb-2 block">Due Date</label>
                            <input type="date" name="due_date" class="w-full bg-indigo-800/50 border border-indigo-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-white transition-all text-white" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                        </div>
                        <button type="submit" class="w-full bg-white text-indigo-900 hover:bg-slate-100 px-6 py-4 rounded-xl font-black text-sm uppercase tracking-[0.1em] transition-all shadow-lg">
                            Generate & Finalize
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 border-dashed">
                    <div class="flex items-center text-slate-500 italic text-xs mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Standard 5% UAE VAT will be automatically applied to each line item.
                    </div>
                    <div class="flex items-center text-slate-500 italic text-xs">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-4.514A9.01 9.01 0 0012 15c4.418 0 8.003-3.045 8.718-7M16.483 20.922a9.043 9.043 0 01-3.483.078" /></svg>
                        Auto-generates FTA-compliant QR code for e-invoicing.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
