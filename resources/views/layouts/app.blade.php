<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>OWN BUSES - Enterprise Fleet Solutions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/images.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#020617] text-slate-200 antialiased overflow-x-hidden">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="w-64 bg-[#0f172a] border-r border-slate-800 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-10 group">
                <img src="{{ asset('images/image.png') }}" alt="Logo" class="h-8 w-auto">
                <h1 class="text-xl font-bold tracking-tight text-white">
                    OWN <span class="text-blue-500">BUSES</span>
                </h1>
            </div>

            <nav class="space-y-2 flex-1 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl bg-blue-600/10 text-blue-400 border border-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>

                <a href="#" class="flex items-center gap-3 p-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition group">
                    <svg class="w-5 h-5 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Fleet Management
                </a>

                <a href="#" class="flex items-center gap-3 p-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition group">
                    <svg class="w-5 h-5 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Bookings
                </a>

                <a href="#" class="flex items-center gap-3 p-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition group">
                    <svg class="w-5 h-5 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Drivers
                </a>

                <a href="#" class="flex items-center gap-3 p-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition group">
                    <svg class="w-5 h-5 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Accounts
                </a>
            </nav>

            <div class="mt-auto border-t border-slate-800 pt-6">
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl text-slate-500 hover:text-red-400 transition text-sm font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sign Out
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Navbar -->
            <header class="h-20 bg-[#0f172a]/80 backdrop-blur-md border-b border-slate-800 flex justify-between items-center px-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-white tracking-tight">Enterprise Dashboard</h2>
                    <span class="px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-wider">Live Analytics</span>
                </div>

                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <button class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                    </div>

                    <div class="flex items-center gap-3 pl-6 border-l border-slate-800">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-white leading-tight">{{ Auth::user()->name ?? 'Guest' }}</p>
                            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">{{ ucwords(str_replace('_', ' ', Auth::user()->role ?? 'Visitor')) }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-700 flex items-center justify-center font-bold shadow-lg shadow-blue-900/20 text-white">
                            {{ substr(Auth::user()->name ?? 'G', 0, 2) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-10 bg-[#020617]">
                @yield('content')
            </main>

        </div>

    </div>

</body>

</html>