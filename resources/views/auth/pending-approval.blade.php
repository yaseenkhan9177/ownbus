@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#020617] relative overflow-hidden py-20">
    <!-- Background Elements -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-0 w-96 h-96 bg-yellow-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-600/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-lg relative z-10 px-6 text-center">
        <div class="bg-slate-900/50 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-yellow-500/10 text-yellow-500 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h2 class="text-3xl font-extrabold text-white mb-4">Application Submitted! 🏁</h2>

            <p class="text-slate-400 mb-6 leading-relaxed">
                Thank you for registering your company. Your application is currently
                <span class="text-yellow-400 font-bold">Under Review</span> by our Super Admin team.
            </p>

            <div class="bg-slate-950/50 rounded-xl p-4 border border-white/5 mb-8">
                <p class="text-sm text-slate-500">
                    We usually process applications within 24 hours. You will receive an email once your account is active.
                </p>
            </div>

            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-white bg-slate-800 hover:bg-slate-700 font-bold py-3 px-6 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Return Home
            </a>
        </div>
    </div>
</div>
@endsection