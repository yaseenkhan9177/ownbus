<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            self::logActivity('created', $model);
        });

        static::updated(function (Model $model) {
            self::logActivity('updated', $model);
        });

        static::deleted(function (Model $model) {
            self::logActivity('deleted', $model);
        });
    }

    protected static function logActivity(string $event, Model $model)
    {
        // CRITICAL: Prevent infinite loop - don't log ActivityLog model itself
        if ($model instanceof ActivityLog) {
            return;
        }

        if (!Auth::check()) return;

        $user = Auth::user();

        // Avoid logging pivot table changes or complex recursion if not needed?
        // Basic implementation for now.

        $properties = null;
        if ($event === 'updated') {
            $properties = [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ];
        } elseif ($event === 'created') {
            $properties = [
                'attributes' => $model->getAttributes(),
            ];
        }

        ActivityLog::create([
            'company_id' => $user->company_id ?? null,
            'user_id' => $user->id,
            'event' => $event,
            'subject_type' => get_class($model),
            'subject_id' => $model->id,
            'description' => class_basename($model) . " was {$event}",
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
