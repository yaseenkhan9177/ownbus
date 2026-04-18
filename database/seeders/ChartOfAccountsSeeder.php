<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Company $company = null): void
    {
        // For isolated tenant DB, we don't strictly need the $company object here 
        // as we are already connected to the tenant DB, but we keep the signature.

        $accounts = [
            // Assets
            ['code' => '1000', 'name' => 'Assets', 'type' => 'asset', 'is_system' => true, 'children' => [
                ['code' => '1010', 'name' => 'Current Assets', 'type' => 'asset', 'children' => [
                    ['code' => '1011', 'name' => 'Cash on Hand', 'type' => 'asset', 'is_system' => true],
                    ['code' => '1012', 'name' => 'Bank Accounts', 'type' => 'asset', 'is_system' => true],
                    ['code' => '1013', 'name' => 'Accounts Receivable', 'type' => 'asset', 'is_system' => true],
                ]],
                ['code' => '1020', 'name' => 'Fixed Assets', 'type' => 'asset', 'children' => [
                    ['code' => '1021', 'name' => 'Fleet Vehicles', 'type' => 'asset'],
                ]],
            ]],

            // Liabilities
            ['code' => '2000', 'name' => 'Liabilities', 'type' => 'liability', 'is_system' => true, 'children' => [
                ['code' => '2010', 'name' => 'Current Liabilities', 'type' => 'liability', 'children' => [
                    ['code' => '2011', 'name' => 'Accounts Payable', 'type' => 'liability', 'is_system' => true],
                    ['code' => '2012', 'name' => 'Security Deposits Held', 'type' => 'liability', 'is_system' => true],
                    ['code' => '2013', 'name' => 'VAT Payable', 'type' => 'liability', 'is_system' => true],
                ]],
            ]],

            // Equity
            ['code' => '3000', 'name' => 'Equity', 'type' => 'equity', 'is_system' => true, 'children' => [
                ['code' => '3999', 'name' => 'Opening Balance Equity', 'type' => 'equity', 'is_system' => true],
                ['code' => '3010', 'name' => 'Retained Earnings', 'type' => 'equity', 'is_system' => true],
            ]],

            // Income
            ['code' => '4000', 'name' => 'Revenue', 'type' => 'income', 'is_system' => true, 'children' => [
                ['code' => '4010', 'name' => 'Rental Income', 'type' => 'income', 'is_system' => true],
                ['code' => '4020', 'name' => 'Fine Income (Recovered)', 'type' => 'income'],
            ]],

            // Expense
            ['code' => '5000', 'name' => 'Expenses', 'type' => 'expense', 'is_system' => true, 'children' => [
                ['code' => '5010', 'name' => 'Operating Expenses', 'type' => 'expense', 'children' => [
                    ['code' => '5011', 'name' => 'Fuel', 'type' => 'expense'],
                    ['code' => '5012', 'name' => 'Maintenance', 'type' => 'expense'],
                    ['code' => '5013', 'name' => 'Salik / Tolls', 'type' => 'expense'],
                    ['code' => '5014', 'name' => 'Fines Paid', 'type' => 'expense'],
                ]],
            ]],
        ];

        $this->createAccounts($accounts);
    }

    private function createAccounts($accounts, $parentId = null)
    {
        foreach ($accounts as $acc) {
            $account = Account::firstOrCreate(
                [
                    'account_code' => $acc['code'],
                ],
                [
                    'parent_id' => $parentId,
                    'account_name' => $acc['name'],
                    'account_type' => $acc['type'],
                    'is_system' => $acc['is_system'] ?? false,
                    'is_active' => true,
                ]
            );

            if (isset($acc['children'])) {
                $this->createAccounts($acc['children'], $account->id);
            }
        }
    }
}
