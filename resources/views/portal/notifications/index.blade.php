@extends('portal.layout')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            @if($notifications->where('read_at', null)->count() > 0)
            <form action="{{ route('portal.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Mark all as read
                </button>
            </form>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
            <div class="p-6 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }} hover:bg-gray-50 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            @if(!$notification->read_at)
                            <span class="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
                            @endif
                            <p class="text-sm font-medium text-gray-900">
                                {{ $notification->data['message'] ?? 'Notification' }}
                            </p>
                        </div>

                        @if(isset($notification->data['vehicle_name']))
                        <p class="text-sm text-gray-600 mt-2 ml-5">
                            Vehicle: {{ $notification->data['vehicle_name'] }}
                        </p>
                        @endif

                        @if(isset($notification->data['pickup_date']))
                        <p class="text-sm text-gray-600 mt-1 ml-5">
                            Date: {{ $notification->data['pickup_date'] }}
                        </p>
                        @endif

                        @if(isset($notification->data['total_amount']))
                        <p class="text-sm text-gray-600 mt-1 ml-5">
                            Amount: AED {{ number_format($notification->data['total_amount'], 2) }}
                        </p>
                        @endif

                        <p class="text-xs text-gray-500 mt-2 ml-5">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-2 ml-4">
                        @if(isset($notification->data['action_url']))
                        <a href="{{ $notification->data['action_url'] }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                            onclick="markAsRead('{{ $notification->id }}')">
                            View
                        </a>
                        @endif

                        <form action="{{ route('portal.notifications.destroy', $notification->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="text-gray-500">No notifications yet</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="p-6 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection