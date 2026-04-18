<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SubscriptionPlan;
use App\Repositories\TransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyAccountingTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'trial_days' => 30, 'is_active' => true, 'features' => []]
        );

        $this->company = Company::factory()->create();

        // Ensure company context is set for BelongsToCompany models
        Account::$currentCompanyId = $this->company->id;
        JournalEntry::$currentCompanyId = $this->company->id;

        $this->repository = new TransactionRepository();
    }

    /**
     * Test getFinancialSummary logic.
     */
    public function test_financial_summary_retrieves_correct_data()
    {
        $incomeAccount = Account::where('account_code', '4010')->first();
        $expenseAccount = Account::where('account_code', '5011')->first();

        if (!$incomeAccount || !$expenseAccount) {
            $this->fail('COA Accounts not seeded.');
        }

        // Create Header
        $journal = JournalEntry::create([
            'company_id' => $this->company->id,
            'date' => now()->toDateString(),
            'description' => 'Test Entry',
            'is_posted' => true,
        ]);

        // Create Lines
        JournalEntryLine::create(['journal_entry_id' => $journal->id, 'account_id' => $incomeAccount->id, 'debit' => 0, 'credit' => 1000]);
        JournalEntryLine::create(['journal_entry_id' => $journal->id, 'account_id' => $expenseAccount->id, 'debit' => 400, 'credit' => 0]);

        $summary = $this->repository->getFinancialSummary($this->company, now()->startOfDay(), now()->endOfDay());

        $this->assertEquals(1000, $summary['income']);
        $this->assertEquals(400, $summary['expense']);
        $this->assertEquals(600, $summary['net_profit']);
    }
}
