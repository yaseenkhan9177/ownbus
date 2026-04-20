@extends('portal.layout')

@section('title', 'Invoice Details | ' . $invoice->invoice_number)

@section('content')
<div class="px-6 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('company.invoices.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-slate-600 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $invoice->invoice_number }}</h1>
                <p class="text-slate-500 text-sm">Issued on {{ $invoice->created_at->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <form action="{{ route('company.invoices.send', $invoice) }}" method="POST">
                @csrf
                <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 00-2 2z" /></svg>
                    Send to Email
                </button>
            </form>
            <a href="{{ route('company.invoices.download', $invoice) }}" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Download PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Invoice Card -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
                <!-- Branding Header -->
                <div class="bg-slate-50 p-10 border-b border-slate-100 flex flex-col md:flex-row justify-between">
                    <div>
                        <div class="h-12 w-auto mb-4">
                            @php
                                $logoPath = 'public/logos/' . Auth::user()->company_id . '.png';
                                $hasLogo = \Illuminate\Support\Facades\Storage::exists($logoPath);
                            @endphp
                            @if($hasLogo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" class="h-full">
                            @else
                                <div class="h-full flex items-center text-slate-800 font-black text-2xl uppercase tracking-tighter italic">
                                    {{ Auth::user()->company->name ?? 'OWNBUS' }}<span class="text-indigo-600">.SOFTWARE</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 font-medium leading-relaxed">
                            <p>{{ Auth::user()->company->address ?? 'Dubai, United Arab Emirates' }}</p>
                            <p>TRN: {{ Auth::user()->company->trn_number ?? '100XXXXXXXXX003' }}</p>
                        </div>
                    </div>
                    <div class="mt-6 md:mt-0 text-right">
                        <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">Tax Invoice</h2>
                        <p class="text-indigo-600 font-bold tracking-widest text-xs uppercase mt-1">VAT Compliant (5%)</p>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="p-10 grid grid-cols-1 md:grid-cols-3 gap-10 border-b border-slate-100 bg-white">
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-3">Bill To</h4>
                        <p class="text-sm font-black text-slate-800 uppercase mb-1">{{ $invoice->customer->name }}</p>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $invoice->customer->address ?? 'No Address' }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $invoice->customer->phone }}</p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-3">Invoice Details</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between md:justify-start md:space-x-4 text-xs">
                                <span class="text-slate-400 font-medium min-w-[80px]">Date:</span>
                                <span class="text-slate-700 font-bold">{{ $invoice->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between md:justify-start md:space-x-4 text-xs">
                                <span class="text-slate-400 font-medium min-w-[80px]">Due Date:</span>
                                <span class="text-slate-700 font-bold">{{ $invoice->due_date->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-3">QR Verification</h4>
                        <div class="w-24 h-24 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200">
                             <!-- Simple placeholder for QR or SVG if library is present -->
                             <svg class="w-16 h-16 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1zm-4 7a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-3zm2 2v-1h1v1h-1z" clip-rule="evenodd" /><path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2H10a1 1 0 01-1-1zM7 16a1 1 0 100 2h3a1 1 0 100-2H7zM2 8a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zM15 16a1 1 0 100 2h1a1 1 0 100-2h-1z" /></svg>
                        </div>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="bg-white">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] border-b border-slate-100">
                                <th class="px-10 py-4 text-left">Description</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4 text-right">Rate</th>
                                <th class="px-6 py-4 text-center">VAT (5%)</th>
                                <th class="px-10 py-4 text-right whitespace-nowrap">Amount (AED)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->items as $item)
                            <tr class="text-sm">
                                <td class="px-10 py-6 font-bold text-slate-700">{{ $item->description }}</td>
                                <td class="px-6 py-6 text-center text-slate-500">{{ $item->quantity }}</td>
                                <td class="px-6 py-6 text-right text-slate-500">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-6 text-center text-slate-500">{{ number_format($item->total - ($item->unit_price * $item->quantity), 2) }}</td>
                                <td class="px-10 py-6 text-right font-bold text-slate-800">{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Section -->
                <div class="px-10 py-12 bg-white flex justify-end">
                    <div class="w-full md:w-80 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400 font-medium">Subtotal Excl. VAT</span>
                            <span class="text-slate-700 font-bold">AED {{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400 font-medium">Total VAT Amount</span>
                            <span class="text-slate-700 font-bold">AED {{ number_format($invoice->vat_amount, 2) }}</span>
                        </div>
                        <div class="h-px bg-slate-100 w-full"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-base font-black text-slate-800 uppercase tracking-tighter">Total Amount Due</span>
                            <div class="text-right">
                                <span class="text-2xl font-black text-indigo-600">AED {{ number_format($invoice->total, 2) }}</span>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Inclusive of VAT</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                @if($invoice->notes)
                <div class="px-10 py-8 bg-slate-50 border-t border-slate-100">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Terms & Notes</h4>
                    <p class="text-xs text-slate-500 italic">{{ $invoice->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Panel -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-6">Payment Status</h3>
                
                <div class="flex items-center space-x-4 mb-8">
                    @php
                        $paidAmount = $invoice->payments->sum('amount');
                        $remaining = max(0, $invoice->total - $paidAmount);
                        $percentage = ($invoice->total > 0) ? ($paidAmount / $invoice->total) * 100 : 0;
                    @endphp
                    <div class="relative w-16 h-16">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path class="text-slate-100" stroke-width="2" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-emerald-500" stroke-dasharray="{{ $percentage }}, 100" stroke-width="2" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-[10px] font-black text-emerald-600">
                            {{ round($percentage) }}%
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Remaining</p>
                        <p class="text-xl font-black text-slate-800">AED {{ number_format($remaining, 2) }}</p>
                    </div>
                </div>

                @if($remaining > 0)
                <form action="{{ route('company.invoices.payments.store', $invoice) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 block">Record Payment</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold font-mono">AED</span>
                            <input type="number" name="amount" step="0.01" value="{{ $remaining }}" max="{{ $remaining }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-12 pr-4 py-3 text-sm focus:outline-none focus:border-emerald-500" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 block">Method</label>
                            <select name="payment_method" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="card">Card</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 block">Date</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-sm uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/10">
                        Record Payment
                    </button>
                </form>
                @else
                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex items-center">
                    <div class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div>
                        <p class="text-emerald-800 font-bold text-sm tracking-tight">Fully Paid</p>
                        <p class="text-emerald-600/70 text-[10px] font-medium tracking-wide">COMPLETED ON {{ $invoice->paid_at->format('d M Y') }}</p>
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-indigo-900 rounded-3xl p-8 shadow-xl text-white">
                <h3 class="text-sm font-bold text-indigo-300 uppercase tracking-widest mb-6">Payment History</h3>
                <div class="space-y-4">
                    @forelse($invoice->payments as $payment)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-white uppercase">{{ $payment->payment_method }}</p>
                            <p class="text-[10px] text-indigo-300">{{ $payment->payment_date->format('M d, Y') }}</p>
                        </div>
                        <span class="text-sm font-black">AED {{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="h-px bg-indigo-800 w-full"></div>
                    @empty
                    <p class="text-xs text-indigo-300 italic">No payments recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
