<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Branch;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Expense;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\ExpenseService;
use App\Services\AccountingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic accounting setup or required models
        \App\Models\SubscriptionPlan::create([
            'name' => 'Starter Plan',
            'slug' => 'starter',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'is_active' => true,
            'trial_days' => 14,
            'version' => 1,
            'features' => [],
        ]);
    }

    public function test_expense_creation_triggers_journal_entry()
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $company->id]);
        $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);
        $user = User::factory()->create(['company_id' => $company->id]);
        $this->actingAs($user);

        // Ensure accounts exist for the company
        $expenseAcc = Account::create([
            'company_id' => $company->id,
            'account_code' => '5011',
            'account_name' => 'Fuel Expense',
            'account_type' => 'expense',
            'is_active' => true,
        ]);

        $cashAcc = Account::create([
            'company_id' => $company->id,
            'account_code' => '1011',
            'account_name' => 'Cash',
            'account_type' => 'asset',
            'is_active' => true,
        ]);

        /** @var ExpenseService $service */
        $service = app(ExpenseService::class);

        $expense = $service->createExpense([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'vehicle_id' => $vehicle->id,
            'category' => 'fuel',
            'description' => 'Weekly Fuel',
            'amount_ex_vat' => 100,
            'vat_percent' => 5,
            'vat_amount' => 5,
            'total_amount' => 105,
            'expense_date' => now(),
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'total_amount' => 105
        ]);

        // Verify Journal Entry
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'App\Models\Expense',
            'reference_id' => $expense->id,
        ]);

        $journal = JournalEntry::where('reference_id', $expense->id)->first();
        $this->assertCount(2, $journal->lines); // Dr Expense, Cr Cash (VAT ignored if no VAT account found in service logic)
    }
}
