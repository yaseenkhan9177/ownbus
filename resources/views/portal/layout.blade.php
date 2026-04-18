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
    </script>
</body>

</html>