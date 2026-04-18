<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Security Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 flex items-center justify-center min-h-screen text-slate-300 font-sans">

    <div class="w-full max-w-sm">
        @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-900/30 border border-rose-500/50 text-rose-400 text-sm font-semibold flex items-center shadow-lg">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-[#0f1524] p-8 rounded-3xl border border-slate-800 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 to-cyan-500"></div>

            <div class="mb-8 text-center">
                <div class="mx-auto w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center border border-slate-800 mb-4 shadow-inner">
                    <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-white tracking-wide">Restricted Access</h1>
                <p class="text-sm text-slate-500 mt-2 font-medium">Enter system Master PIN to continue.</p>
            </div>

            <form method="POST" action="{{ route('super-admin.pin.verify') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="pin" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Master PIN</label>
                    <input type="password" id="pin" name="pin" required autofocus
                        class="block w-full px-5 py-4 bg-slate-900/50 border border-slate-700 rounded-xl text-center tracking-[0.5em] text-2xl text-white font-mono placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all text-security-disc"
                        placeholder="••••••">
                    @error('pin')
                    <p class="text-rose-400 text-xs font-bold mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-950 focus:ring-cyan-500 transition-all">
                    Verify Identity
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ url('/') }}" class="text-xs text-slate-500 hover:text-slate-300 transition-colors uppercase tracking-widest font-bold">&larr; Return to main site</a>
            </div>
        </div>
    </div>
</body>

</html>