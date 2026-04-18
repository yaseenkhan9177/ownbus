<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GlobalSetting extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'company_id',
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Clear the cache boundary upon mutating any explicit setting.
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            $cacheKey = $setting->company_id ? "global_settings_{$setting->company_id}" : "global_settings_system";
            Cache::forget($cacheKey);
            // Also forget legacy cache just in case
            Cache::forget('global_settings');
        });

        static::deleted(function ($setting) {
            $cacheKey = $setting->company_id ? "global_settings_{$setting->company_id}" : "global_settings_system";
            Cache::forget($cacheKey);
            Cache::forget('global_settings');
        });
    }
}
