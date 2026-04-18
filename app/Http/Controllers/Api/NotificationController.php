<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List all notifications
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(20);

        return NotificationResource::collection($notifications)->additional([
            'success' => true,
            'message' => 'Notifications retrieved successfully',
        ]);
    }

    /**
     * Get unread notification count
     */
    public function unread(Request $request)
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count,
            ],
        ]);
    }

    /**
     * Mark notification as read
     */
    public function read(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Get notification preferences
     */
    public function preferences(Request $request)
    {
        $preferences = $request->user()->notification_preferences ?? [
            'email' => true,
            'sms' => false,
            'whatsapp' => false,
        ];

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email' => 'boolean',
            'sms' => 'boolean',
            'whatsapp' => 'boolean',
        ]);

        $request->user()->update([
            'notification_preferences' => $validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated',
            'data' => $validated,
        ]);
    }
}
