<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Company;

/**
 * Centralized audit logging service for enterprise compliance
 *
 * Logs all critical actions for security and audit trails to both DB and logs.
 */
class AuditLogger
{
    /**
     * Internal helper to save to DB and Log
     */
    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        array $oldValues = [],
        array $newValues = [],
        array $metadata = [],
        ?int $companyId = null,
        ?int $userId = null
    ): void {
        $userId = $userId ?? Auth::id();
        $companyId = $companyId ?? (Auth::user()->company_id ?? null);

        // 1. Save to Database
        AuditLog::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'http_method' => request()->method(),
        ]);

        // 2. Fallback to file logs for redundancy
        Log::info("AuditLog: {$action}", [
            'user_id' => $userId,
            'company_id' => $companyId,
            'entity' => "{$entityType}#{$entityId}",
            'ip' => request()->ip()
        ]);
    }

    /**
     * Log subscription lifecycle event
     */
    public function logSubscriptionChange(
        int $companyId,
        string $eventType,
        array $context = []
    ): void {
        $this->log(
            action: 'subscription_changed',
            entityType: 'Subscription',
            metadata: array_merge($context, ['event_type' => $eventType]),
            companyId: $companyId
        );
    }

    /**
     * Log payment event
     */
    public function logPaymentEvent(
        int $companyId,
        string $eventType,
        float $amount,
        string $status,
        array $context = []
    ): void {
        $this->log(
            action: 'payment_' . $status,
            entityType: 'Payment',
            metadata: array_merge($context, [
                'event_type' => $eventType,
                'amount' => $amount,
                'status' => $status
            ]),
            companyId: $companyId
        );
    }

    /**
     * Log rental action
     */
    public function logRentalAction(
        int $rentalId,
        int $companyId,
        string $action,
        ?int $userId = null,
        array $context = []
    ): void {
        $this->log(
            action: $action,
            entityType: 'Rental',
            entityId: $rentalId,
            metadata: $context,
            companyId: $companyId,
            userId: $userId
        );
    }

    /**
     * Log pricing rule update
     */
    public function logPricingUpdate(
        int $companyId,
        string $action,
        array $changes = []
    ): void {
        $this->log(
            action: 'pricing_' . $action,
            entityType: 'PricingRule',
            oldValues: $changes['old'] ?? [],
            newValues: $changes['new'] ?? [],
            companyId: $companyId
        );
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(
        string $eventType,
        string $severity = 'warning',
        array $context = []
    ): void {
        $this->log(
            action: 'security_' . $eventType,
            metadata: array_merge($context, ['severity' => $severity])
        );
    }

    /**
     * Log system error
     */
    public function logSystemError(
        string $component,
        string $message,
        \Throwable $exception = null
    ): void {
        // System errors usually go to daily logs only unless critical
        Log::channel('daily')->error("System Error in {$component}: {$message}", [
            'exception' => $exception ? $exception->getMessage() : null
        ]);
    }
}
