<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerCodeService
{
    /**
     * Generate a unique customer code per company (CUS-XXXXX)
     */
    public function generate(): string
    {
        return DB::transaction(function () {
            // Get the last customer code, using lock for update to avoid race conditions
            $lastCustomer = Customer::query()
                ->whereNotNull('customer_code')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;

            if ($lastCustomer && preg_match('/CUS-(\d+)/', $lastCustomer->customer_code, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }

            return 'CUS-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
