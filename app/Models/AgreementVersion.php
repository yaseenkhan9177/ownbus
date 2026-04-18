<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'content',
        'active',
    ];

    public function acceptances()
    {
        return $this->hasMany(AgreementAcceptance::class, 'version', 'version');
    }
}
