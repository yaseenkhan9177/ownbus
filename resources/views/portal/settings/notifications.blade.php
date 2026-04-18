@extends('portal.layout')

@section('title', 'Notification Settings')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Notification Preferences</h1>
            <p class="text-gray-600 mt-1">Choose how you want to receive notifications about your bookings</p>
        </div>

        <!-- Form -->
        <form action="{{ route('portal.settings.notifications.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Email Notifications -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="email"
                            name="email"
                            type="checkbox"
                            {{ $preferences['email'] ?? true ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="ml-3">
                        <label for="email" class="font-medium text-gray-900">Email Notifications</label>
                        <p class="text-sm text-gray-500">Receive booking confirmations, receipts, and reminders via email</p>
                        <div class="mt-2 text-xs text-gray-400">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- SMS Notifications -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="sms"
                            name="sms"
                            type="checkbox"
                            {{ $preferences['sms'] ?? false ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            {{ auth()->user()->phone ? '' : 'disabled' }}>
                    </div>
                    <div class="ml-3">
                        <label for="sms" class="font-medium text-gray-900 {{ auth()->user()->phone ? '' : 'text-gray-400' }}">
                            SMS Notifications
                            @if(!auth()->user()->phone)
                            <span class="text-xs text-red-500 ml-2">(Phone number required)</span>
                            @endif
                        </label>
                        <p class="text-sm text-gray-500">Get text messages for urgent booking updates</p>
                        @if(auth()->user()->phone)
                        <div class="mt-2 text-xs text-gray-400">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            {{ auth()->user()->phone }}
                        </div>
                        @else
                        <div class="mt-2 text-xs text-orange-600">
                            <a href="#" class="underline">Add phone number to enable SMS</a>
                        </div>
                        @endif
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- WhatsApp Notifications -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="whatsapp"
                            name="whatsapp"
                            type="checkbox"
                            {{ $preferences['whatsapp'] ?? false ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            {{ auth()->user()->phone ? '' : 'disabled' }}>
                    </div>
                    <div class="ml-3">
                        <label for="whatsapp" class="font-medium text-gray-900 {{ auth()->user()->phone ? '' : 'text-gray-400' }}">
                            WhatsApp Notifications
                            @if(!auth()->user()->phone)
                            <span class="text-xs text-red-500 ml-2">(Phone number required)</span>
                            @endif
                        </label>
                        <p class="text-sm text-gray-500">Receive booking updates on WhatsApp</p>
                        <p class="text-xs text-blue-600 mt-1">Coming soon - Free alternative to SMS</p>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">What notifications will I receive?</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Booking confirmation when you create a reservation</li>
                                    <li>Payment receipt after successful payment</li>
                                    <li>Reminder 24 hours before your pickup time</li>
                                    <li>Cancellation confirmation if you cancel a booking</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <a href="{{ route('portal.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-md font-medium mr-3">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Save Preferences
                </button>
            </div>
        </form>
    </div>
</div>
@endsection