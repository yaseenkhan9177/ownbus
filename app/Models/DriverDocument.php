<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'driver_profile_id',
        'document_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'file_path',
        'is_verified',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_verified' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(DriverProfile::class, 'driver_profile_id');
    }
}
