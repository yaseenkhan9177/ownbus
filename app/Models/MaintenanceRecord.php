<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRecord extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes, LogsActivity;

    protected static function booted()
    {
        static::observe(\App\Observers\MaintenanceRecordObserver::class);
    }

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const TYPE_PREVENTIVE = 'preventive';
    public const TYPE_CORRECTIVE = 'corrective';
    public const TYPE_ACCIDENT = 'accident';
    public const TYPE_INSPECTION = 'inspection';
    public const TYPE_INSURANCE = 'insurance';

    protected $fillable = [
        'branch_id',
        'vehicle_id',
        'maintenance_number',
        'type',
        'status',
        'scheduled_date',
        'start_date',
        'completed_date',
        'next_due_date',
        'odometer_reading',
        'total_cost',
        'vendor_id',
        'description',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_date' => 'datetime',
        'completed_date' => 'datetime',
        'next_due_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(MaintenanceItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Customer::class, 'vendor_id'); // Using Customer for vendors as standard per ERP
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
