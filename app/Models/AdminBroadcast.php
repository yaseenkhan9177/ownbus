<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminBroadcast extends Model
{
    protected $fillable = [
        'company_id',
        'target_role',
        'message',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
