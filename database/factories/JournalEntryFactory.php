<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        return [
            'transaction_id' => \App\Models\FinancialTransaction::factory(),
            'account_id' => \App\Models\Account::factory(),
            'debit' => 0,
            'credit' => 0,
        ];
    }
}
