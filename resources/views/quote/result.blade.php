@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#020617] relative overflow-hidden py-20">

    <!-- Background Elements -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600/20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-600/20 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-4xl p-6 relative z-10">

        <div class="text-center mb-10">
            <h2 class="text-4xl font-extrabold text-white mb-2">Quote Ready! 🚍</h2>
            <p class="text-slate-400">Great news, we found the perfect vehicle for your trip.</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Vehicle Card -->
            <div class="lg:col-span-2 bg-slate-900/50 backdrop-blur-xl border border-white/5 rounded-3xl p-8 shadow-2xl">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <span class="inline-block px-3 py-1 bg-green-500/10 text-green-400 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                            {{ $count }} Application(s) Available
                        </span>
                        <h3 class="text-3xl font-bold text-white">{{ $vehicle->type }}</h3>
                        <p class="text-slate-400 mt-1">Premium fleet option</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-500 uppercase tracking-widest font-bold">Daily Rate</p>
                        <p class="text-2xl font-bold text-white">AED {{ number_format($vehicle->daily_rate, 0) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-slate-950/50 p-4 rounded-2xl border border-white/5">
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-1">Pickup</p>
                        <p class="text-white font-medium">{{ $pickup->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="bg-slate-950/50 p-4 rounded-2xl border border-white/5">
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-1">Drop-off</p>
                        <p class="text-white font-medium">{{ $dropoff->format('d M Y, h:i A') }}</p>
                    </div>
                </div>

                <div class="border-t border-white/5 pt-6">
                    <h4 class="text-white font-bold mb-4">Included Features:</h4>
                    <ul class="grid grid-cols-2 gap-3">
                        <li class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Professional Driver
                        </li>
                        <li class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Fuel & Salik
                        </li>
                        <li class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Comprehensive Insurance
                        </li>
                        <li class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            24/7 Support
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Total & Action -->
            <div class="bg-linear-to-b from-blue-600 to-indigo-700 rounded-3xl p-8 shadow-2xl flex flex-col justify-between relative overflow-hidden">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>

                <div class="relative z-10">
                    <p class="text-blue-200 font-bold uppercase tracking-widest text-xs mb-2">Total Estimate</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-5xl font-black text-white">AED {{ number_format($totalPrice, 0) }}</span>
                    </div>
                    <p class="text-blue-100 mt-2 text-sm">for {{ $quantity }} Bus(es) / {{ $days }} Day(s)</p>
                </div>

                <div class="relative z-10 mt-8">
                    <form action="{{ route('quote.book') }}" method="POST">
                        @csrf
                        <input type="hidden" name="vehicle_ids" value="{{ $vehicleIds }}">
                        <input type="hidden" name="pickup_time" value="{{ $pickup }}">
                        <input type="hidden" name="dropoff_time" value="{{ $dropoff }}">
                        <input type="hidden" name="total_price" value="{{ $totalPrice }}">

                        <button type="submit" class="w-full bg-white text-blue-600 font-bold py-4 rounded-xl shadow-lg hover:bg-slate-50 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
                            <span>Request {{ $quantity }} Bus(es) Now</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                    </form>
                    <a href="{{ route('welcome') }}" class="block text-center text-blue-200 text-sm font-semibold mt-4 hover:text-white transition">Back to Search</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection