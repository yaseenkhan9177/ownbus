<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Customer extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes, Auditable;

    protected static function booted()
    {
        static::observe(\App\Observers\CustomerObserver::class);
    }

    // Type Constants
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_CORPORATE = 'corporate';

    // Status Constants
    const STATUS_ACTIVE = 'active';
    const STATUS_BLACKLISTED = 'blacklisted';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'user_id',
        'branch_id',
        'customer_code',
        'type',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'alternate_phone',
        'national_id',
        'driving_license_no',
        'driving_license_expiry',
        'address',
        'city',
        'country',
        'credit_limit',
        'current_balance',
        'is_credit_blocked',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'driving_license_expiry' => 'date',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_credit_blocked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['name', 'risk_level'];

    /**
     * Get full name attribute
     */
    public function getNameAttribute()
    {
        if ($this->type === self::TYPE_CORPORATE) {
            return $this->company_name;
        }
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Risk Indicator Attribute
     * Red -> blacklisted
     * Yellow -> balance >= 80% of limit
     * Green -> safe
     */
    public function getRiskLevelAttribute()
    {
        if ($this->status === self::STATUS_BLACKLISTED) {
            return 'red';
        }

        if ($this->credit_limit > 0) {
            $threshold = $this->credit_limit * 0.8;
            if ($this->current_balance >= $threshold) {
                return 'yellow';
            }
        }

        return 'green';
    }

    /**
     * Determine if this customer is blocked from new rentals.
     * A customer is blocked if:
     * - `is_credit_blocked` flag is manually set, OR
     * - Their current AR balance >= their credit limit (auto-block)
     */
    public function isCreditBlocked(): bool
    {
        if ($this->is_credit_blocked) {
            return true;
        }

        if ($this->credit_limit > 0 && $this->current_balance >= $this->credit_limit) {
            return true;
        }

        return false;
    }

    // Relationships

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }
}
