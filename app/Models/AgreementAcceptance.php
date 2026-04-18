<?php

namespace App\Models;

use App\Models\Traits\ScopedByCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementAcceptance extends Model
{
    use HasFactory, ScopedByCompany;

    protected $fillable = [
        'company_id',
        'version',
        'signed_by',
        'ip_address',
        'content_hash',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function agreementVersion()
    {
        return $this->belongsTo(AgreementVersion::class, 'version', 'version');
    }
}
