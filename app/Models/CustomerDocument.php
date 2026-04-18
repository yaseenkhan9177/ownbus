<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDocument extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'document_type',
        'document_number',
        'expiry_date',
        'file_path',
        'file_name',
        'file_type',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
