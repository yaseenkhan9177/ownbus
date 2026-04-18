<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'contract_id',
        'document_type',
        'file_path',
        'file_name',
        'file_type',
        'notes',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
