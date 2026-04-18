<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anomaly extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'type',
        'severity',
        'description',
        'detected_value',
        'expected_value',
        'related_model_type',
        'related_model_id',
        'status',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'detected_value' => 'decimal:2',
        'expected_value' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    public function relatedModel()
    {
        return $this->morphTo();
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
