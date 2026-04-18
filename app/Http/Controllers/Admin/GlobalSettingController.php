<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Cache;

class GlobalSettingController extends Controller
{
    /**
     * Display all settings grouped by category for the Admin UI.
     */
    public function index()
    {
        // Cache the entire settings payload for 24 hours (cleared automatically on save)
        $settings = Cache::remember('global_settings', 86400, function () {
            return GlobalSetting::all();
        });

        // Group the settings dynamically for the tabs
        $groupedSettings = $settings->groupBy('group');

        return view('admin.settings.index', compact('groupedSettings'));
    }

    /**
     * Bulk update settings from a specific UI Tab.
     */
    public function update(Request $request)
    {
        $payload = $request->except(['_token', '_method']);

        foreach ($payload as $key => $value) {
            $setting = GlobalSetting::where('key', $key)->first();

            if ($setting) {
                // Handle Checkboxes/Booleans which might not be submitted if unchecked
                if ($setting->type === 'boolean') {
                    $setting->value = $value;
                } else {
                    $setting->value = is_array($value) ? json_encode($value) : $value;
                }
                $setting->save();
            }
        }

        // Catch explicitly unchecked booleans that were omitted from the POST payload entirely
        $allBooleans = GlobalSetting::where('type', 'boolean')->get();
        foreach ($allBooleans as $boolSetting) {
            if (!array_key_exists($boolSetting->key, $payload)) {
                $boolSetting->value = '0';
                $boolSetting->save();
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Platform configurations successfully updated.');
    }
}
