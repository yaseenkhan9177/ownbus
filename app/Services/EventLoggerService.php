<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use App\Models\EventLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * EventLoggerService
 * Black box recorder for centralized, structured activity logs.
 */
class EventLoggerService
{
    // High-level Event Types
    public const RENTAL_CREATED    = 'rental_created';
    public const RENTAL_UPDATED    = 'rental_updated';
    public const RENTAL_COMPLETED  = 'rental_completed';
    public const EXPENSE_RECORDED  = 'expense_recorded';
    public const FINE_ADDED        = 'fine_added';
    public const FINE_PAID         = 'fine_paid';
    public const FINE_RECOVERED    = 'fine_recovered';
    public const CONTRACT_ACTIVE   = 'contract_activated';
    public const CONTRACT_EXPIRED  = 'contract_expired';
    public const CREDIT_BLOCKED    = 'credit_blocked';
    public const RISK_ESCALATED    = 'risk_escalated';
    public const MAINTENANCE_LOG   = 'maintenance_logged';

    // Severities
    public const SEVERITY_INFO     = 'info';
    public const SEVERITY_WARNING  = 'warning';
    public const SEVERITY_CRITICAL = 'critical';

    /**
     * Log a system event.
     */
    public function log(
        Company $company,
        string $eventType,
        Model $entity,
        string $title,
        array $meta = [],
        string $severity = self::SEVERITY_INFO,
        ?User $user = null,
        ?Branch $branch = null
    ): void {
        EventLog::create([
            'company_id'   => $company->id,
            'branch_id'    => $branch?->id ?? (Auth::user()?->branch_id),
            'event_type'   => $eventType,
            'severity'     => $severity,
            'entity_type'  => get_class($entity),
            'entity_id'    => $entity->id,
            'title'        => $title,
            'meta'         => $meta,
            'performed_by' => $user?->id ?? Auth::id(),
            'occurred_at'  => now(),
        ]);
    }
}
