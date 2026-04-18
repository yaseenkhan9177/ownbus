<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use App\Services\EventLoggerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * LogsEvents Trait
 * Provides easy access to the EventLoggerService from models or controllers.
 */
trait LogsEvents
{
    /**
     * Log a system event.
     */
    protected function logEvent(
        Company $company,
        string $eventType,
        Model $entity,
        string $title,
        array $meta = [],
        string $severity = EventLoggerService::SEVERITY_INFO,
        ?User $user = null,
        ?Branch $branch = null
    ): void {
        App::make(EventLoggerService::class)->log(
            $company,
            $eventType,
            $entity,
            $title,
            $meta,
            $severity,
            $user,
            $branch
        );
    }
}
