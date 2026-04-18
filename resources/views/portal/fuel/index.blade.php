@extends('layouts.company')

@section('title', 'Fuel Management')

@section('content')
<div class="space-y-6" x-data="fuelManager()">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-1 uppercase">FUEL_LOG</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Kinetic Energy & Operational Cost Analysis</p>
        </div>
        <button type="button" @click="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <i class="bi bi-fuel-pump-fill"></i> RECORD_REFILL
        </button>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="bi bi-bucket-fill text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">TOTAL_VOLUME</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_liters'], 2) }} <span class="text-xs text-slate-400 uppercase">L</span></div>
                <div class="mt-2 text-[9px] font-bold text-blue-500 uppercase tracking-tighter">Fleet-wide Consumption</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="bi bi-cash-stack text-6xl text-emerald-500"></i>
            </div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">OPERATIONAL_COST</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_cost'], 2) }} <span class="text-xs text-slate-400 uppercase">{{ auth()->user()->company->currency ?? 'AED' }}</span></div>
                <div class="mt-2 text-[9px] font-bold text-emerald-500 uppercase tracking-tighter">Total Expenditure</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="bi bi-tag-fill text-6xl text-amber-500"></i>
            </div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">AVG_PRICE_UNIT</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['avg_price'], 2) }}</div>
                <div class="mt-2 text-[9px] font-bold text-amber-500 uppercase tracking-tighter">Market Equilibrium</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="bi bi-clock-history text-6xl text-rose-500"></i>
            </div>
            <div class="relative z-10">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">PENDING_REPORTS</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['pending_cnt'] }}</div>
                <div class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tighter">Awaiting Verification</div>
            </div>
        </div>
    </div>

    {{-- Pending Driver Reports --}}
    @if($pendingReports->isNotEmpty())
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-rose-100 dark:border-rose-900/30 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-rose-50/50 dark:bg-rose-900/10 border-b border-rose-100 dark:border-rose-900/30 flex items-center justify-between">
            <h3 class="text-xs font-black text-rose-600 uppercase tracking-widest flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill animate-pulse"></i> PENDING_OPERATOR_SUBMISSIONS
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">OPERATOR</th>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">UNIT</th>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">REFILL_DATA</th>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">TEMPORAL_MARK</th>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @foreach($pendingReports as $report)
                    <tr class="hover:bg-rose-50/20 dark:hover:bg-rose-900/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ $report->driver->user->name ?? 'VOID_ID' }}</div>
                            <div class="text-[10px] font-bold text-slate-400 tracking-tighter">{{ $report->driver->driver_code ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs font-black text-slate-600 dark:text-slate-400 uppercase">
                            {{ $report->vehicle->vehicle_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-black text-emerald-600 uppercase">{{ number_format($report->metadata['fuel_cost'] ?? 0, 2) }} {{ auth()->user()->company->currency ?? 'AED' }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $report->metadata['fuel_liters'] ?? 0 }} L</div>
                        </td>
                        <td class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase">
                            {{ $report->reported_at->format('d M, H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" 
                                    @click='approveReport(@json($report), @json($report->metadata))'
                                    class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                                REVIEW_&_POST
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Main Fuel Logs Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                <i class="bi bi-list-task text-blue-500"></i> OPERATIONAL_FUEL_HISTORY
            </h3>
            
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <select name="vehicle_id" class="bg-slate-100 dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest focus:ring-2 focus:ring-blue-500 appearance-none min-w-[120px]">
                    <option value="">ALL_UNITS</option>
                    @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                    @endforeach
                </select>
                <input type="date" name="from_date" class="bg-slate-100 dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-[10px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest focus:ring-2 focus:ring-blue-500" value="{{ request('from_date') }}">
                <button class="p-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/10">
                    <i class="bi bi-funnel-fill text-xs"></i>
                </button>
                <a href="{{ route('company.fuel.index') }}" class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                    <i class="bi bi-x-lg text-xs"></i>
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-50 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">TEMPORAL_MARK</th>
                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">KINETIC_UNIT</th>
                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">TELEMETRY_MARK (KM)</th>
                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">VOLUME (L)</th>
                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">UNIT_PRICE</th>
                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">TOTAL_COST</th>
                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ $log->date->format('d M Y') }}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">BY: {{ $log->creator->name ?? 'SYSTEM' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase">{{ $log->vehicle->vehicle_number ?? 'VOID' }}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase truncate max-w-[100px]">{{ $log->vehicle->name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-black text-slate-600 dark:text-slate-400">{{ number_format($log->odometer_reading) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-xs font-black text-blue-600 dark:text-blue-400">{{ number_format($log->liters, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-[10px] font-bold text-slate-400">
                            {{ number_format($log->cost_per_liter, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-xs font-black text-slate-900 dark:text-white">{{ number_format($log->total_amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2 pr-2">
                                <a href="{{ route('company.fuel.show', $log) }}" class="p-2 text-slate-400 hover:text-blue-500 bg-slate-50 dark:bg-slate-800 rounded-lg group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20 transition-all">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <form action="{{ route('company.fuel.destroy', $log) }}" method="POST" onsubmit="return confirm('INITIATE_DELETION_PROTOCOL?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-400 hover:text-rose-500 bg-slate-50 dark:bg-slate-800 rounded-lg group-hover:bg-rose-50 dark:group-hover:bg-rose-900/20 transition-all">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-200 dark:text-slate-700">
                                    <i class="bi bi-fuel-pump text-3xl"></i>
                                </div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest text">ZERO_FUEL_LOGS_DETECTED</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    {{-- Redesigned Modal --}}
    <div x-show="modalOpen" 
         x-cloak
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" 
                 @click="closeModal()" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white dark:bg-slate-950 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800">
                
                <form method="POST" action="{{ route('company.fuel.store') }}">
                    @csrf
                    <input type="hidden" name="report_id" x-model="formData.report_id">
                    
                    <div class="px-8 pt-8 pb-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">FUEL_ENTRY_PROTOCOL</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kinetic Energy Input Log</p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                                <i class="bi bi-fuel-pump-fill text-xl"></i>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">VEHICLE_UNIT</label>
                                <select name="vehicle_id" x-model="formData.vehicle_id" class="w-full bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none" required>
                                    <option value="">SELECT_UNIT</option>
                                    @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->vehicle_number }} — {{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">ENTRY_DATE</label>
                                <input type="date" name="date" x-model="formData.date" class="w-full bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:border-blue-500 transition-all" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">ODOMETER (KM)</label>
                                <input type="number" name="odometer_reading" x-model="formData.odometer" class="w-full bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:border-blue-500 transition-all" placeholder="0" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">VOLUME (L)</label>
                                <input type="number" step="0.01" name="liters" x-model="formData.liters" @input="calcTotal()" class="w-full bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:border-blue-500 transition-all" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">RATE (AED/L)</label>
                                <input type="number" step="0.01" name="cost_per_liter" x-model="formData.rate" @input="calcTotal()" class="w-full bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:border-blue-500 transition-all" required>
                            </div>

                            <div class="md:col-span-2">
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border-2 border-blue-100 dark:border-blue-900/30 flex items-center justify-between">
                                    <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">TOTAL_CALCULATED_COST</span>
                                    <div class="flex items-baseline gap-1">
                                        <input type="number" step="0.01" name="total_amount" x-model="formData.total" class="bg-transparent border-none text-right font-black text-blue-600 dark:text-blue-400 text-xl w-24 focus:ring-0 p-0" readonly required>
                                        <span class="text-[10px] font-black text-blue-400 uppercase">{{ auth()->user()->company->currency ?? 'AED' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/50 px-8 py-6 flex flex-col-reverse sm:flex-row gap-3">
                        <button type="button" @click="closeModal()" class="flex-1 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-slate-200 dark:border-slate-700 hover:bg-slate-50 transition-all">
                            ABORT_ENTRY
                        </button>
                        <button type="submit" class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20">
                            VALIDATE_&_PERSIST_DATA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function fuelManager() {
    return {
        modalOpen: false,
        formData: {
            report_id: '',
            vehicle_id: '',
            date: '{{ date("Y-m-d") }}',
            odometer: '',
            liters: '',
            rate: '',
            total: ''
        },
        openModal() {
            this.modalOpen = true;
            this.formData = {
                report_id: '',
                vehicle_id: '',
                date: '{{ date("Y-m-d") }}',
                odometer: '',
                liters: '',
                rate: '',
                total: ''
            };
        },
        closeModal() {
            this.modalOpen = false;
        },
        calcTotal() {
            if(this.formData.liters && this.formData.rate) {
                this.formData.total = (parseFloat(this.formData.liters) * parseFloat(this.formData.rate)).toFixed(2);
            }
        },
        approveReport(report, metadata) {
            this.formData.report_id = report.id;
            this.formData.vehicle_id = report.vehicle_id;
            this.formData.date = report.reported_at.split('T')[0];
            this.formData.odometer = metadata.odometer || '';
            this.formData.liters = metadata.fuel_liters || '';
            this.formData.total = metadata.fuel_cost || '';
            if(this.formData.liters > 0) {
                this.formData.rate = (this.formData.total / this.formData.liters).toFixed(2);
            }
            this.modalOpen = true;
        }
    }
}
</script>
@endsection
