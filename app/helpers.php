<?php

use Illuminate\Support\Facades\Cache;
use App\Models\GlobalSetting;

if (!function_exists('system_setting')) {
    /**
     * Get a globally configured system setting or a tenant-scoped system setting.
     * Uses Cache to prevent repetitive database queries.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function system_setting($key, $default = null)
    {
        $companyId = auth()->check() ? auth()->user()->company_id : null;
        
        $cacheKey = $companyId ? "global_settings_{$companyId}" : "global_settings_system";

        $settings = Cache::remember($cacheKey, now()->addDays(7), function () use ($companyId) {
            $query = GlobalSetting::query();
            
            if ($companyId) {
                // Fetch both system default (company_id null) and tenant override
                $query->whereNull('company_id')->orWhere('company_id', $companyId);
            } else {
                $query->whereNull('company_id');
            }

            return $query->get()->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->value];
            })->toArray();
        });

        return $settings[$key] ?? $default;
    }
}
