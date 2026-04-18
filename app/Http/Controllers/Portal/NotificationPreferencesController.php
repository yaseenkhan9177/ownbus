<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationPreferencesController extends Controller
{
    /**
     * Show notification preferences page
     */
    public function edit()
    {
        $preferences = auth()->user()->notification_preferences ?? [
            'email' => true,
            'sms' => false,
            'whatsapp' => false,
        ];

        return view('portal.settings.notifications', compact('preferences'));
    }

    /**
     * Update notification preferences
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'email' => 'boolean',
            'sms' => 'boolean',
            'whatsapp' => 'boolean',
        ]);

        // Set default to true if not provided (unchecked checkboxes don't send values)
        $preferences = [
            'email' => $request->has('email'),
            'sms' => $request->has('sms'),
            'whatsapp' => $request->has('whatsapp'),
        ];

        auth()->user()->update([
            'notification_preferences' => $preferences
        ]);

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}
