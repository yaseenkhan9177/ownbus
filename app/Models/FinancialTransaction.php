<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $table = 'journal_entries'; // Redirect to new enterprise naming

    protected $fillable = [
        'branch_id',
        'reference_type',
        'reference_id',
        'date', // Renamed from transaction_date
        'description',
        'is_posted',
        'posted_at',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    /**
     * Legacy Alias for backward compatibility.
     */
    public function journalEntries()
    {
        return $this->lines();
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
