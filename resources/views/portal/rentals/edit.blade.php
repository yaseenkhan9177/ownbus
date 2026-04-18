@extends('layouts.company')

@section('title', 'Refine Quote - Rental #' . $rental->rental_number)

@section('header_title')
<div class="flex items-center space-x-2">
    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">Refine Mission</h1>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto animate-in fade-in slide-in-from-bottom-2 duration-500" x-data="rentalForm()">
    <form action="{{ route('company.rentals.update', $rental) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column: Configuration --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. Client & Schedule --}}
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-1.5 h-4 bg-cyan-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Modified Parameters</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Target Client</label>
                            <select name="customer_id" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $rental->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Assignment Type</label>
                            <select name="rental_type" x-model="rentalType" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                                <option value="daily" {{ $rental->rental_type == 'daily' ? 'selected' : '' }}>Daily Rental</option>
                                <option value="hourly" {{ $rental->rental_type == 'hourly' ? 'selected' : '' }}>Hourly Rental</option>
                                <option value="monthly" {{ $rental->rental_type == 'monthly' ? 'selected' : '' }}>Monthly Subscription</option>
                                <option value="distance" {{ $rental->rental_type == 'distance' ? 'selected' : '' }}>Distance Based</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pickup Schedule</label>
                            <input type="datetime-local" name="start_date" required value="{{ $rental->start_date->format('Y-m-d\TH:i') }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Release Schedule</label>
                            <input type="datetime-local" name="end_date" required value="{{ $rental->end_date->format('Y-m-d\TH:i') }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                </div>

                {{-- 2. Assets & Logistics --}}
                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Asset Calibration</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Assigned Unit</label>
                            <select name="vehicle_id" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                                <option value="">Draft Assignment...</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ $rental->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vehicle_number }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Field Agent (Driver)</label>
                            <select name="driver_id" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                                <option value="">Self-Drive / Later...</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ $rental->driver_id == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pickup Vector</label>
                            <input type="text" name="pickup_location" required value="{{ $rental->pickup_location }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-cyan-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Return Vector</label>
                            <input type="text" name="dropoff_location" value="{{ $rental->dropoff_location }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Operational Analytics (Notes)</label>
                    <textarea name="notes" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-cyan-500">{{ $rental->notes }}</textarea>
                </div>
            </div>

            {{-- Right Column: Financial Matrix --}}
            <div class="space-y-6">
                <div class="bg-slate-900 dark:bg-white dark:text-slate-900 text-white rounded-3xl p-6 shadow-2xl relative overflow-hidden">
                    {{-- Decor --}}
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-500/20 rounded-full blur-3xl"></div>

                    <div class="relative">
                        <div class="flex items-center space-x-2 mb-6">
                            <div class="w-1 h-3 bg-emerald-500 rounded-full"></div>
                            <h2 class="text-[10px] font-black uppercase tracking-widest opacity-60">Matrix Calibration</h2>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[8px] font-black uppercase tracking-[0.2em] mb-1 opacity-50">Base Rate Scale</label>
                                <select name="rate_type" x-model="rateType" class="w-full bg-white/10 dark:bg-slate-100 border-none rounded-xl px-4 py-2 text-xs font-black uppercase tracking-widest text-white dark:text-slate-900 focus:ring-1 focus:ring-cyan-500 mb-2">
                                    <option value="daily" {{ $rental->rate_type == 'daily' ? 'selected' : '' }}>Per Day</option>
                                    <option value="weekly" {{ $rental->rate_type == 'weekly' ? 'selected' : '' }}>Per Week</option>
                                    <option value="monthly" {{ $rental->rate_type == 'monthly' ? 'selected' : '' }}>Per Month</option>
                                </select>
                                <input type="number" name="rate_amount" x-model.number="rateAmount" step="0.01" required
                                    class="w-full bg-white/10 dark:bg-slate-100 border-none rounded-xl px-4 py-3 text-xl font-black text-white dark:text-slate-900 focus:ring-1 focus:ring-cyan-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div>
                                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] mb-1 opacity-50">Security Bond</label>
                                    <input type="number" name="security_deposit" x-model.number="securityDeposit" step="0.01"
                                        class="w-full bg-white/10 dark:bg-slate-100 border-none rounded-xl px-4 py-2 text-xs font-black text-white dark:text-slate-900 focus:ring-1 focus:ring-cyan-500">
                                </div>
                                <div>
                                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] mb-1 opacity-50">Incentive (Discount)</label>
                                    <input type="number" name="discount" x-model.number="discount" step="0.01"
                                        class="w-full bg-white/10 dark:bg-slate-100 border-none rounded-xl px-4 py-2 text-xs font-black text-white dark:text-slate-900 focus:ring-1 focus:ring-cyan-500">
                                </div>
                            </div>

                            <div class="border-t border-white/10 dark:border-slate-200 pt-4 space-y-2">
                                <div class="flex justify-between text-[10px] font-bold">
                                    <span class="opacity-50 uppercase">Sub-Total</span>
                                    <span x-text="formatCurrency(subtotal())"></span>
                                </div>
                                <div class="flex justify-between text-[10px] font-bold">
                                    <span class="opacity-50 uppercase">VAT (5%)</span>
                                    <span x-text="formatCurrency(vat())"></span>
                                </div>
                                <div class="flex justify-between text-lg font-black pt-2 text-emerald-400 dark:text-emerald-600">
                                    <span class="uppercase tracking-widest text-xs self-center">Updated Total</span>
                                    <span x-text="formatCurrency(total())"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-900 rounded-2xl text-xs font-black uppercase tracking-[0.2em] transition-all transform hover:scale-[1.02] active:scale-95 shadow-xl shadow-emerald-500/20">
                            Update Quote Matrix
                        </button>

                        <a href="{{ route('company.rentals.show', $rental) }}" class="block text-center mt-4 text-[10px] font-black uppercase tracking-widest text-white/40 hover:text-white transition-colors">
                            Abort Changes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function rentalForm() {
        return {
            rentalType: '{{ $rental->rental_type }}',
            rateType: '{{ $rental->rate_type }}',
            rateAmount: {
                {
                    (float) $rental - > rate_amount
                }
            },
            securityDeposit: {
                {
                    (float) $rental - > security_deposit
                }
            },
            discount: {
                {
                    (float) $rental - > discount
                }
            },

            subtotal() {
                return Math.max(0, this.rateAmount - this.discount);
            },
            vat() {
                return this.subtotal() * 0.05;
            },
            total() {
                return this.subtotal() + this.vat();
            },
            formatCurrency(value) {
                return new Intl.NumberFormat('en-AE', {
                    style: 'currency',
                    currency: 'AED'
                }).format(value);
            }
        }
    }
</script>
@endsection