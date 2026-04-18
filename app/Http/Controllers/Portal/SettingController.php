<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;

class SettingController extends Controller
{
    /**
     * Display the settings form for the Company Administrator.
     */
    public function index()
    {
        $companyId = auth()->user()->company_id;
        if (!$companyId) {
            abort(403);
        }

        $globalSettings = GlobalSetting::whereNull('company_id')->get()->keyBy('key');
        $tenantSettings = GlobalSetting::where('company_id', $companyId)->get()->keyBy('key');

        $mergedSettings = $globalSettings->map(function ($setting, $key) use ($tenantSettings) {
            if ($tenantSettings->has($key)) {
                return $tenantSettings->get($key);
            }
            return $setting;
        });

        $groupedSettings = $mergedSettings->groupBy('group');

        return view('portal.settings.system', compact('groupedSettings'));
    }

    /**
     * Update Company overrides.
     */
    public function update(Request $request)
    {
        $companyId = auth()->user()->company_id;
        if (!$companyId) {
            abort(403);
        }

        $payload = $request->except(['_token', '_method']);

        foreach ($payload as $key => $value) {
            $globalSetting = GlobalSetting::whereNull('company_id')->where('key', $key)->first();
            if (!$globalSetting) {
                continue;
            }

            $setting = GlobalSetting::firstOrNew([
                'company_id' => $companyId,
                'key' => $key
            ]);

            $setting->type = $globalSetting->type;
            $setting->group = $globalSetting->group;
            
            if ($setting->type === 'boolean') {
                $setting->value = $value;
            } else {
                $setting->value = is_array($value) ? json_encode($value) : $value;
            }
            $setting->save();
        }

        // Deal with unchecked booleans for this tenant
        $allBooleans = GlobalSetting::whereNull('company_id')->where('type', 'boolean')->get();
        foreach ($allBooleans as $boolSetting) {
            if (!array_key_exists($boolSetting->key, $payload)) {
                $setting = GlobalSetting::firstOrNew([
                    'company_id' => $companyId,
                    'key' => $boolSetting->key
                ]);
                $setting->type = 'boolean';
                $setting->group = $boolSetting->group;
                $setting->value = '0';
                $setting->save();
            }
        }

        return redirect()->route('company.system-preferences.index')->with('success', 'Company preferences seamlessly updated.');
    }
}
