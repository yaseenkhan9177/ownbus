<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, Auditable, SoftDeletes;
    protected $connection = 'tenant';

    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_DRAFT = 'draft';

    protected $fillable = [
        'branch_id',
        'customer_id',
        'vehicle_id',
        'driver_id',
        'contract_number',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'contract_value',
        'monthly_rate',
        'extra_charges',
        'discount',
        'billing_cycle',
        'last_billed_at',
        'next_billing_at',
        'next_billing_date',
        'status',
        'auto_renew',
        'payment_terms',
        'payment_due_date',
        'terms',
        'notes',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'last_billed_at'    => 'date',
        'next_billing_at'   => 'date',
        'next_billing_date' => 'date',
        'payment_due_date'  => 'date',
        'auto_renew'        => 'boolean',
        'contract_value'    => 'decimal:2',
        'monthly_rate'      => 'decimal:2',
        'extra_charges'     => 'decimal:2',
        'discount'          => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function invoices()
    {
        return $this->hasMany(ContractInvoice::class);
    }

    public function documents()
    {
        return $this->hasMany(ContractDocument::class);
    }

    public function isExpiringSoon(): bool
    {
        $expiryDate        = $this->end_date->startOfDay();
        $thirtyDaysFromNow = now()->startOfDay()->addDays(30);
        return $expiryDate->isFuture() && $expiryDate->lte($thirtyDaysFromNow);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Used by ContractBillingService to check if billing is due today.
     * Checks canonical next_billing_date field.
     */
    public function isDueToBill(): bool
    {
        // First billing — never billed yet
        if (!$this->next_billing_date && !$this->next_billing_at) {
            return true;
        }
        // Prefer canonical next_billing_date, fall back to legacy next_billing_at
        $nextDate = $this->next_billing_date ?? $this->next_billing_at;
        return now()->gte($nextDate);
    }
}
