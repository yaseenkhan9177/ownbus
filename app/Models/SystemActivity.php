<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemActivity extends Model
{
    protected $fillable = [
        'tenant_id',
        'action',
        'description',
    ];

    public function tenant()
    {
        return $this->belongsTo(Company::class, 'tenant_id');
    }
}
