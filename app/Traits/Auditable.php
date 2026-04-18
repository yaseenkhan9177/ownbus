<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Jobs\LogAuditJob;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::dispatchAuditJob('create', $model);
        });

        static::updated(function (Model $model) {
            self::dispatchAuditJob('update', $model);
        });

        static::deleted(function (Model $model) {
            self::dispatchAuditJob('delete', $model);
        });
    }

    protected static function dispatchAuditJob(string $action, Model $model)
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        $oldData = null;
        $newData = null;

        if ($action === 'update') {
            $oldData = $model->getOriginal();
            $newData = $model->getChanges();
        } elseif ($action === 'create') {
            $newData = $model->getAttributes();
        } elseif ($action === 'delete') {
            $oldData = $model->getAttributes();
        }

        $logData = [
            'user_id' => $user->id,
            'company_id' => $user->company_id ?? null,
            'action' => $action,
            'module' => class_basename($model),
            'reference_id' => $model->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'http_method' => Request::method(),
            'metadata' => [
                'table' => $model->getTable()
            ]
        ];

        // Dispatch to background queue
        dispatch(new LogAuditJob($logData));
    }
}
