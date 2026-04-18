<?php

namespace App\Exceptions;

use App\Models\Customer;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreditLimitExceededException extends HttpException
{
    public readonly Customer $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;

        $outstanding = number_format($customer->current_balance ?? 0, 2);
        $limit       = number_format($customer->credit_limit ?? 0, 2);

        $message = $customer->is_credit_blocked
            ? "Credit blocked: {$customer->name} has been manually credit-blocked by management."
            : "Credit limit exceeded: {$customer->name} has an outstanding balance of AED {$outstanding} (limit: AED {$limit}).";

        parent::__construct(422, $message);
    }
}
