<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes, LogsActivity;

    // Status Constants
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'branch_id',
        'driver_code',
        'first_name',
        'last_name',
        'phone',
        'email',
        'national_id',
        'license_number',
        'license_expiry_date',
        'rta_permit_expiry',
        'visa_expiry',
        'emirates_id_expiry',
        'license_type',
        'hire_date',
        'salary',
        'commission_rate',
        'status',
        'address',
        'city',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'license_expiry_date' => 'date',
        'rta_permit_expiry' => 'date',
        'visa_expiry' => 'date',
        'emirates_id_expiry' => 'date',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'created_by' => 'integer',
    ];

    // Relationships
    public function rentals()
    {
        return $this->hasMany(Rental::class);
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

    /**
     * Scope for available drivers.
     * Driver is available if:
     * - active
     * - license not expired
     * - no active rental
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('license_expiry_date', '>=', now()->toDateString())
            ->whereDoesntHave('rentals', function ($q) {
                $q->where('status', Rental::STATUS_ACTIVE);
            });
    }

    /**
     * Get full name attribute.
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get all UAE compliance documents with their risk status.
     * Returns array with label, expiry, is_expired, days_left, risk ('red'|'orange'|'green')
     */
    public function getUaeComplianceStatus(int $warningDays = 30): array
    {
        $checks = [
            'Driving License'   => $this->license_expiry_date,
            'RTA Permit'        => $this->rta_permit_expiry,
            'Residence Visa'    => $this->visa_expiry,
            'Emirates ID'       => $this->emirates_id_expiry,
        ];

        $results = [];
        foreach ($checks as $label => $date) {
            if (!$date) {
                continue;
            }
            $daysLeft = (int) now()->diffInDays($date, false);
            $results[] = [
                'label'      => $label,
                'expiry'     => $date,
                'is_expired' => $date->isPast(),
                'days_left'  => max(0, $daysLeft),
                'risk'       => $date->isPast() ? 'red' : ($daysLeft <= $warningDays ? 'orange' : 'green'),
            ];
        }
        return $results;
    }

    /**
     * Check if driver has any compliance risk (expiring or expired docs).
     */
    public function hasComplianceRisk(int $warningDays = 30): bool
    {
        $threshold = now()->addDays($warningDays);
        return ($this->license_expiry_date && $this->license_expiry_date->lte($threshold))
            || ($this->rta_permit_expiry && $this->rta_permit_expiry->lte($threshold))
            || ($this->visa_expiry && $this->visa_expiry->lte($threshold))
            || ($this->emirates_id_expiry && $this->emirates_id_expiry->lte($threshold));
    }

    /**
     * Get active black points for the driver (last 12 months).
     */
    public function blackPoints(): int
    {
        return (int) VehicleFine::where('driver_id', $this->id)
            ->where('fine_date', '>=', now()->subYear())
            ->sum('black_points');
    }
}
