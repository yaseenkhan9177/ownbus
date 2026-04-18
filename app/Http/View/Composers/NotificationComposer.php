<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $systemNotifications = collect();

        if (Auth::check() && Auth::user()->company_id) {
            // Notifications are cross-database, so we just query the tenant DB for the current user
            $dbNotifications = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->latest()
                ->take(10)
                ->get();

            $systemNotifications = $dbNotifications->map(function ($notification) {
                // Map the DB urgency to the UI severity classes
                $severity = 'info';
                if ($notification->urgency === 'warning') $severity = 'warning';
                if ($notification->urgency === 'critical') $severity = 'error';

                // Map type to category icon
                $category = 'System';
                if (in_array($notification->type, ['fine', 'payment_due'])) $category = 'Finance';
                if (in_array($notification->type, ['expiry', 'maintenance'])) $category = 'Vehicle';
                if (in_array($notification->type, ['license_expiry', 'visa_expiry', 'geofence'])) $category = 'Driver';

                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'severity' => $severity,
                    'category' => $category,
                    'link' => '#', // Can be dynamically resolved based on type later
                    'time' => $notification->created_at->diffForHumans(),
                ];
            });
        }

        $view->with('systemNotifications', $systemNotifications);
    }
}
