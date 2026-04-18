@extends('layouts.company')

@section('title', 'Dynamic Pricing Engine')
@section('header_title', 'Operational Intelligence')

@section('content')
<div class="space-y-8" x-data="pricingCalculator()">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight uppercase italic">Dynamic Pricing Engine</h2>
            <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-bold">Real-time rate optimization & calculator</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Calculator Column --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Live Rate Calculator
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Select Vehicle</label>
                        <select x-model="form.vehicle_id" class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500 transition">
                            <option value="">Choose a vehicle...</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->name }} ({{ $vehicle->plate_number }}) - {{ ucfirst($vehicle->type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Branch (Utilization Context)</label>
                        <select x-model="form.branch_id" class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500 transition">
                            <option value="">Select branch...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Customer (Risk Context)</label>
                        <select x-model="form.customer_id" class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500 transition">
                            <option value="">Select customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Rental Start Date</label>
                        <input type="date" x-model="form.start_date" class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <button @click="calculate()" :disabled="loading" class="w-full py-3.5 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-black uppercase tracking-widest rounded-xl transition shadow-lg shadow-blue-900/40 flex items-center justify-center gap-2">
                        <template x-if="loading">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="loading ? 'Processing...' : 'Generate Quote'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Results Comparison Column --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Multiplier Breakdown --}}
            <div x-show="result" x-transition.opacity class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-xl relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-500/5 rounded-full"></div>
                
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-8">Quote Breakdown</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="flex justify-between items-center group">
                            <span class="text-sm text-slate-400 group-hover:text-slate-300 transition">Base Daily Rate</span>
                            <span class="text-lg font-black text-white" x-text="'AED ' + numberFormat(result.base_rate)"></span>
                        </div>
                        
                        <div class="space-y-3">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-800 pb-1">Optimization Signals</p>
                            
                            <template x-for="(val, label) in result.breakdown">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-slate-500 capitalize" x-text="label.replace(/_/g, ' ')"></span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold" :class="val > 1 ? 'text-rose-400' : (val < 1 ? 'text-emerald-400' : 'text-slate-400')" x-text="'× ' + val"></span>
                                        <svg x-show="val > 1" class="w-3 h-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                        <svg x-show="val < 1" class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-blue-600/5 border border-blue-500/20 rounded-2xl p-6 flex flex-col justify-center items-center text-center">
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Optimized Daily Rate</p>
                        <p class="text-5xl font-black text-white tracking-tighter" x-text="'AED ' + numberFormat(result.optimized_rate)"></p>
                        <p class="text-xs text-slate-500 mt-4 max-w-[200px]">This rate includes logic for utilization pressure, seasonality, and customer risk profiling.</p>
                    </div>
                </div>
            </div>

            {{-- Rules Table --}}
            <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                <div class="px-6 py-4 bg-slate-800/50 border-b border-slate-800">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest">Active Seasonal Rules</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                <th class="px-6 py-4">Rule/Season</th>
                                <th class="px-6 py-4">Branch</th>
                                <th class="px-6 py-4">Period</th>
                                <th class="px-6 py-4 text-right">Multiplier</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($rules as $rule)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4">
                                    <p class="text-xs font-bold text-white">{{ $rule->name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-slate-400">{{ $rule->branch?->name ?? 'Global' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[10px] text-slate-500 font-bold">{{ $rule->start_date->format('d M') }} — {{ $rule->end_date->format('d M') }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs font-black {{ $rule->multiplier > 1 ? 'text-rose-400' : 'text-emerald-400' }}">×{{ number_format($rule->multiplier, 2) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-xs text-slate-500">No active seasonal rules found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function pricingCalculator() {
    return {
        form: {
            vehicle_id: '',
            branch_id: '',
            customer_id: '',
            start_date: new Date().toISOString().split('T')[0]
        },
        loading: false,
        result: null,

        async calculate() {
            if (!this.form.vehicle_id || !this.form.branch_id || !this.form.customer_id) {
                alert('Please select vehicle, branch and customer');
                return;
            }

            this.loading = true;
            try {
                const response = await fetch('{{ route("company.pricing.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                if (!response.ok) throw new Error('API Error');
                
                this.result = await response.json();
            } catch (error) {
                console.error(error);
                alert('Failed to calculate rate');
            } finally {
                this.loading = false;
            }
        },

        numberFormat(val) {
            return new Intl.NumberFormat('en-AE', { minimumFractionDigits: 0 }).format(val);
        }
    }
}
</script>
@endpush
@endsection
