<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class SettingsController extends Controller
{
    /**
     * Display company settings.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $company = $user->company;
        $settings = \App\Models\NotificationSetting::firstOrCreate([]);

        return view('portal.settings.index', compact('company', 'settings'));
    }

    /**
     * Update company settings.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $company = $user->company;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'currency' => 'required|string|size:3',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'trn_number' => 'nullable|string|max:50',
            'invoice_prefix' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:1024', // 1MB Max
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('companies/logos', 'public');
            $validated['logo_url'] = '/storage/' . $path;
        }

        $company->update($validated);

        if ($request->has('notification_settings')) {
            $notificationSettings = $request->input('notification_settings');
            \App\Models\NotificationSetting::updateOrCreate(
                [],
                $notificationSettings
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
