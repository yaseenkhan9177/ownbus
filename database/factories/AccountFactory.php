<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'account_code' => $this->faker->unique()->numberBetween(1000, 9999),
            'account_name' => $this->faker->word,
            'account_type' => 'asset',
            'is_system' => false,
            'is_active' => true,
        ];
    }
}
