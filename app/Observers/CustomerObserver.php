<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Company;
use App\Services\CustomerCodeService;
use App\Services\EventLoggerService;
use App\Traits\LogsEvents;
use Illuminate\Support\Facades\Auth;

class CustomerObserver
{
    use LogsEvents;

    protected $codeService;

    public function __construct(CustomerCodeService $codeService)
    {
        $this->codeService = $codeService;
    }

    /**
     * Handle the Customer "creating" event.
     */
    public function creating(Customer $customer): void
    {
        if (empty($customer->customer_code)) {
            $customer->customer_code = $this->codeService->generate();
        }
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        // 1. Manual Credit Block
        if ($customer->isDirty('is_credit_blocked') && $customer->is_credit_blocked) {
            $this->logEvent(
                Auth::user()->company,
                EventLoggerService::CREDIT_BLOCKED,
                $customer,
                "Customer {$customer->name} manually credit blocked",
                ['reason' => $customer->notes],
                EventLoggerService::SEVERITY_WARNING
            );
        }

        // 2. Auto Credit Block (Balance limit)
        if ($customer->isDirty('current_balance')) {
            $oldBalance = $customer->getOriginal('current_balance');
            $newBalance = $customer->current_balance;

            if ($customer->credit_limit > 0 && $oldBalance < $customer->credit_limit && $newBalance >= $customer->credit_limit) {
                $this->logEvent(
                    Auth::user()->company,
                    EventLoggerService::CREDIT_BLOCKED,
                    $customer,
                    "Customer {$customer->name} auto-blocked due to balance (AED {$newBalance})",
                    ['balance' => $newBalance, 'limit' => $customer->credit_limit],
                    EventLoggerService::SEVERITY_CRITICAL
                );
            }
        }
    }
}
