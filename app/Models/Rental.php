<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rental extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes, Auditable;

    protected static function booted()
    {
        static::observe(\App\Observers\RentalObserver::class);
        static::observe(\App\Observers\RentalCacheObserver::class);

        static::creating(function ($rental) {
            if (empty($rental->rental_number)) {
                $year  = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $rental->rental_number = 'RNT-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Status Constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // Payment Status Constants
    public const PAYSTATUS_UNPAID = 'unpaid';
    public const PAYSTATUS_PARTIAL = 'partial';
    public const PAYSTATUS_PAID = 'paid';

    protected $fillable = [
        'uuid',
        'branch_id',
        'customer_id',
        'vehicle_id',
        'driver_id',
        'rental_number',
        'contract_no',
        'rental_type',
        'rate_type',
        'rate_amount',
        'start_date',
        'end_date',
        'actual_start_datetime',
        'actual_return_date',
        'pickup_location',
        'dropoff_location',
        'odometer_start',
        'odometer_end',
        'final_amount',
        'tax',
        'discount',
        'security_deposit',
        'payment_status',
        'status',
        'notes',
        'created_by',
        'coupon_id',
        'pricing_adjustments',
        'pdf_path',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'actual_start_datetime' => 'datetime',
        'actual_return_date' => 'datetime',
        'rate_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'pricing_adjustments' => 'array',
        'vehicle_id' => 'integer',
        'customer_id' => 'integer',
        'driver_id' => 'integer',
        'created_by' => 'integer',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

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
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(RentalItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(RentalStatusLog::class);
    }

    public function transactions()
    {
        return $this->morphMany(FinancialTransaction::class, 'reference');
    }

    public function payments()
    {
        return $this->hasMany(RentalPayment::class);
    }
}
