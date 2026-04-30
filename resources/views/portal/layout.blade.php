<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Customer Portal')</title>

    <!-- Tailwind CSS CDN (for quick development) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#00BCD4">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="OwnBus">
    <link rel="apple-touch-icon" href="/images/icon-192.png">

    <style>
    .status-online {
        color: #10B981;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .status-offline {
        color: #EF4444;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .online-dot {
        width: 8px;
        height: 8px;
        background: #10B981;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    .offline-dot {
        width: 8px;
        height: 8px;
        background: #EF4444;
        border-radius: 50%;
    }
    .sync-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        max-width: 300px;
    }
    .sync-notification.success {
        background: #064E3B;
        border: 1px solid #10B981;
        color: #10B981;
    }
    .sync-notification.warning {
        background: #78350F;
        border: 1px solid #F59E0B;
        color: #F59E0B;
    }
    .sync-notification.info {
        background: #1E3A5F;
        border: 1px solid #00BCD4;
        color: #00BCD4;
    }
    .install-app-btn {
        background: #00BCD4;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.875rem;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.3s;
    }
    .install-app-btn:hover {
        background: #0097A7;
    }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('portal.vehicles.index') }}" class="text-2xl font-bold text-blue-600">
                        BusRental Pro
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('portal.vehicles.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Browse Vehicles
                    </a>

                    @auth
                    @if(auth()->user()->role === 'customer')
                    <a href="{{ route('portal.dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        My Dashboard
                    </a>
                    <a href="{{ route('portal.rentals.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        My Rentals
                    </a>
                    <a href="{{ route('portal.settings.notifications') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Settings
                    </a>
                    @endif
                    @endauth
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-4">
                    <!-- PWA Indicators -->
                    <div id="connection-status" class="status-online">
                        <span class="online-dot"></span> Online
                    </div>
                    <button id="install-btn" class="hidden install-app-btn">
                        📱 Install App
                    </button>

                    @auth
                    @if(auth()->user()->role === 'customer')
                    <!-- Notification Bell -->
                    <div x-data="{ open: false, unreadCount: {{ auth()->user()->unreadNotifications->count() }} }" class="relative">
                        <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span x-show="unreadCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full" x-text="unreadCount"></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 border border-gray-200" style="display: none;">
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Notifications</h3>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('portal.notifications.mark-all-read') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">Mark all read</button>
                                </form>
                                @endif
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse(auth()->user()->notifications->take(5) as $notification)
                                <a href="{{ $notification->data['action_url'] ?? '#' }}"
                                    class="block p-4 hover:bg-gray-50 border-b border-gray-100 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}"
                                    onclick="markAsRead('{{ $notification->id }}')">
                                    <p class="text-sm font-medium text-gray-900">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </a>
                                @empty
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    No notifications yet
                                </div>
                                @endforelse
                            </div>
                            @if(auth()->user()->notifications->count() > 5)
                            <div class="p-3 border-t border-gray-200 text-center">
                                <a href="{{ route('portal.notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View all notifications</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <span class="text-gray-700 text-sm">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Logout
                        </button>
                    </form>
                    @endif
                    @else
                    <a href="{{ route('portal.login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Login
                    </a>
                    <a href="{{ route('portal.register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Register
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="container mx-auto px-4 mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="container mx-auto px-4 mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">BusRental Pro</h3>
                    <p class="text-gray-400">Professional bus rental services for all your transportation needs.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('portal.vehicles.index') }}" class="text-gray-400 hover:text-white">Browse Vehicles</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <p class="text-gray-400">Email: info@busrent alpro.com</p>
                    <p class="text-gray-400">Phone: +971 XX XXX XXXX</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} BusRental Pro. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script src="/js/offline.js"></script>
    <script>
        function markAsRead(notificationId) {
            fetch(`/portal/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
        }

        // PWA Install Prompt
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button
            document.getElementById('install-btn')
                ?.classList.remove('hidden');
        });

        document.getElementById('install-btn')
            ?.addEventListener('click', async () => {
            
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const result = await deferredPrompt
                    .userChoice;
                
                if (result.outcome === 'accepted') {
                    console.log('App installed!');
                }
                deferredPrompt = null;
            }
        });
    </script>
</body>

</html>