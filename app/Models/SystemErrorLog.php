<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'url',
        'error_message',
        'stack_trace',
    ];

    public function tenant()
    {
        return $this->belongsTo(Company::class, 'tenant_id');
    }
}
