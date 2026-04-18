<?php

namespace Database\Factories;

use App\Models\FinancialTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialTransactionFactory extends Factory
{
    protected $model = FinancialTransaction::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'transaction_date' => now(),
            'description' => $this->faker->sentence,
            'reference_type' => 'App\Models\Rental',
            'reference_id' => 1,
        ];
    }
}
