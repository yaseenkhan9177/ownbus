<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceItem extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    public const TYPE_PART = 'part';
    public const TYPE_LABOR = 'labor';
    public const TYPE_SERVICE = 'service';

    protected $fillable = [
        'maintenance_record_id',
        'item_type',
        'description',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function record()
    {
        return $this->belongsTo(MaintenanceRecord::class, 'maintenance_record_id');
    }
}
