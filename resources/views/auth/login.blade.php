@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#020617] relative overflow-hidden">

    <!-- Background Elements -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600/20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-600/20 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-md p-8 relative z-10">
        <div class="text-center mb-10">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/image.png') }}" alt="Logo" class="h-12 w-auto">
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
            <p class="text-slate-400">Sign in to your enterprise dashboard</p>
        </div>

        <div class="bg-[#0f172a]/50 backdrop-blur-xl border border-white/5 rounded-3xl p-8 shadow-2xl">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="space-y-6">
                    @if ($errors->any())
                    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                        <ul class="list-disc list-inside text-sm text-red-400">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-400 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" required
                            class="w-full px-4 py-3 bg-slate-900/50 border @error('email') border-red-500 @else border-slate-700 @enderror rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="name@company.com" value="{{ old('email') }}">
                        @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-400 mb-2">Password</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 bg-slate-900/50 border @error('password') border-red-500 @else border-slate-700 @enderror rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="••••••••">
                        @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-slate-700 text-blue-600 focus:ring-blue-500 bg-slate-900">
                            <label for="remember" class="ml-2 text-sm text-slate-400">Remember me</label>
                        </div>
                        <a href="#" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Forgot password?</a>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-900/20 transition-all transform hover:-translate-y-0.5 active:scale-95">
                        Sign In
                    </button>

                    <div class="text-center pt-4 border-t border-white/5">
                        <p class="text-sm text-slate-500">
                            Don't have an account? <a href="#" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors">Contact Support</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection