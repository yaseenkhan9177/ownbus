@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#020617] relative overflow-hidden py-20">
    <!-- Background Elements -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-600/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-2xl relative z-10 px-6">
        <div class="text-center mb-10">
            <h2 class="text-4xl font-extrabold text-white mb-2">Partner with Us 🤝</h2>
            <p class="text-slate-400">Join the leading bus rental network in the UAE.</p>
        </div>

        <div class="bg-slate-900/50 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl">
            @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 text-sm font-semibold">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 text-sm font-semibold">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('register.company.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Company Details -->
                <div class="border-b border-white/5 pb-6">
                    <h3 class="text-white font-bold text-lg mb-4">Company Details</h3>
                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-600">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">TRN Number</label>
                            <input type="text" name="trn_number" value="{{ old('trn_number') }}"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-600">
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Company Logo</label>
                            <input type="file" name="logo" accept="image/*" required
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition-colors cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Owner Details -->
                <div class="border-b border-white/5 pb-6">
                    <h3 class="text-white font-bold text-lg mb-4">Owner Details</h3>
                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Owner Name</label>
                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-600">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mobile Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-600">
                        </div>
                    </div>
                </div>

                <!-- Login Details -->
                <div class="border-b border-white/5 pb-6">
                    <h3 class="text-white font-bold text-lg mb-4">Login Access</h3>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Official Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-600">
                        </div>
                        <div class="grid md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Password</label>
                                <input type="password" name="password" required
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-600">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Confirm Password</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-600">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legal -->
                <div class="flex items-start gap-3">
                    <input type="checkbox" name="agreed_to_terms" id="agreed_to_terms" required
                        class="mt-1 w-4 h-4 rounded border-slate-700 text-blue-600 focus:ring-blue-500 bg-slate-900">
                    <label for="agreed_to_terms" class="text-sm text-slate-400">
                        I agree to the <a href="#" class="text-blue-400 hover:text-blue-300">Terms & Conditions</a> and confirm that all provided information is accurate.
                    </label>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-900/40 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
                    Submit Registration
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 mt-8">
            Already registered? <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-bold">Login here</a>
        </p>
    </div>
</div>
@endsection