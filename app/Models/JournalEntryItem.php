<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryItem extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'ledger_id',
        'debit',
        'credit',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
}
