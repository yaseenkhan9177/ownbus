<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Driver Portal')</title>
    <link rel="icon" type="image/png" href="/images/images.png">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            overscroll-behavior: none;
        }

        /* ── App Shell ─────────────────────────────────────── */
        .app-shell {
            max-width: 430px;
            margin: 0 auto;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            background: #0f172a;
            position: relative;
        }

        /* ── Top Header ────────────────────────────────────── */
        .app-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-bottom: 1px solid rgba(20, 184, 166, 0.15);
            padding: 1rem 1.25rem 0.875rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        /* ── Scrollable Content ────────────────────────────── */
        .app-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
            padding-bottom: 6rem;
            /* space for bottom nav */
        }

        /* ── Bottom Navigation ─────────────────────────────── */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 430px;
            background: #1e293b;
            border-top: 1px solid rgba(20, 184, 166, 0.2);
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0.5rem 0 max(0.5rem, env(safe-area-inset-bottom));
            z-index: 100;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
            padding: 0.4rem 1.2rem;
            border-radius: 0.75rem;
            text-decoration: none;
            color: #64748b;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s;
        }

        .nav-item.active,
        .nav-item:hover {
            color: #14b8a6;
            background: rgba(20, 184, 166, 0.08);
        }

        .nav-item svg {
            width: 1.5rem;
            height: 1.5rem;
        }

        /* ── Cards ─────────────────────────────────────────── */
        .card {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .card-teal {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            border: none;
        }

        /* ── Buttons ─────────────────────────────────────────  */
        .btn {
            display: block;
            width: 100%;
            padding: 0.875rem;
            border-radius: 0.875rem;
            font-size: 0.875rem;
            font-weight: 700;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-teal {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            color: #fff;
        }

        .btn-teal:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 1.5px solid rgba(20, 184, 166, 0.4);
            color: #14b8a6;
        }

        .btn-outline:hover {
            background: rgba(20, 184, 166, 0.08);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
        }

        /* ── Form Inputs ────────────────────────────────────── */
        .form-input {
            width: 100%;
            background: #0f172a;
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            color: #f1f5f9;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: #14b8a6;
        }

        .form-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 0.4rem;
        }

        /* ── Badges ──────────────────────────────────────────  */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 99rem;
            font-size: 0.625rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .badge-teal {
            background: rgba(20, 184, 166, 0.15);
            color: #14b8a6;
        }

        .badge-amber {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .badge-rose {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        .badge-emerald {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .badge-slate {
            background: rgba(100, 116, 139, 0.15);
            color: #94a3b8;
        }

        /* ── Alert ────────────────────────────────────────── */
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* ── Scrollbar ────────────────────────────────────── */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        @yield('extra-styles')
    </style>
</head>

<body>
    <div class="app-shell">

        {{-- Flash messages --}}
        <div id="flash-container" style="position: fixed; top: 1rem; left: 50%; transform: translateX(-50%); width: calc(100% - 2rem); max-width: 400px; z-index: 200;">
            @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Top Header --}}
        <header class="app-header">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #14b8a6; margin: 0;">Driver Portal</p>
                    <h1 style="font-size: 1.1rem; font-weight: 800; color: #f1f5f9; margin: 0.1rem 0 0;">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.625rem; background: rgba(20,184,166,0.12); display: flex; align-items: center; justify-content: center; border: 1px solid rgba(20,184,166,0.2);">
                    <svg width="16" height="16" fill="none" stroke="#14b8a6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="app-content">
            @yield('content')
        </main>

        {{-- Bottom Navigation --}}
        @if(session('driver_id'))
        <nav class="bottom-nav">
            <a href="{{ route('driver.dashboard') }}" class="nav-item {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Home
            </a>
            <a href="{{ route('driver.fuel.create') }}" class="nav-item {{ request()->routeIs('driver.fuel.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3m0 0l-4 4m4-4l4 4M21 14a5 5 0 01-5 5H9" />
                </svg>
                Fuel
            </a>
            <a href="{{ route('driver.breakdown.create') }}" class="nav-item {{ request()->routeIs('driver.breakdown.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Report
            </a>
            <a href="{{ route('driver.profile') }}" class="nav-item {{ request()->routeIs('driver.profile') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile
            </a>
        </nav>
        @endif

    </div>

    <script>
        // Auto-dismiss flash messages after 3s
        setTimeout(() => {
            const fc = document.getElementById('flash-container');
            if (fc) fc.style.transition = 'opacity 0.5s', fc.style.opacity = '0';
        }, 3000);
    </script>

    @stack('scripts')
</body>

</html>