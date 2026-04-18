<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ledger extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'account_type',
    ];

    public function entries()
    {
        return $this->hasMany(JournalEntryItem::class);
    }
}
