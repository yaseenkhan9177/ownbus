<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleFine extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    // Status Constants
    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_UNDER_PROCESSING = 'under-processing';
    public const STATUS_APPEALED = 'appealed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'branch_id',
        'vehicle_id',
        'driver_id',
        'customer_id',
        'rental_id',
        'responsible_type',
        'journal_entry_id',
        'source',
        'authority',
        'fine_type',
        'fine_number',
        'fine_date',
        'due_date',
        'amount',
        'black_points',
        'status',
        'paid_at',
        'payment_reference',
        'description',
        'attachment_path',
        'created_by',
    ];

    protected $casts = [
        'fine_date'            => 'date',
        'due_date'             => 'date',
        'paid_at'              => 'datetime',
        'black_points'         => 'integer',
        'amount'               => 'decimal:2',
    ];

    // Trigger: VehicleFineObserver::updated() in AppServiceProvider
    // Observer fires processCustomerRecovery() when status → 'paid' and customer_responsible = true

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function payments()
    {
        return $this->hasMany(FinePayment::class, 'vehicle_fine_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending'
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function isRecovered(): bool
    {
        return !is_null($this->journal_entry_id);
    }
}
