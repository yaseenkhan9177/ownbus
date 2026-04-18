<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SubscriptionPlan;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ProfitLossAccuracyTest
 *
 * Tests:
 *  1. Net profit calculated correctly for known revenue & expense entries
 *  2. Multiple revenue categories sum correctly
 *  3. Multiple expense categories sum correctly
 *  4. Period filter only includes entries within range
 */
class ProfitLossAccuracyTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected FinancialReportService $service;
    protected Account $cashAccount;
    protected Account $incomeAccount;
    protected Account $expenseAccount;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'trial_days' => 30, 'is_active' => true, 'features' => []]
        );

        $this->company = Company::factory()->create();
        $this->service = new FinancialReportService();

        $this->cashAccount    = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();
        $this->incomeAccount  = Account::where('company_id', $this->company->id)->where('account_code', '4010')->first();
        $this->expenseAccount = Account::where('company_id', $this->company->id)->where('account_type', 'expense')->first();

        if (!$this->cashAccount || !$this->incomeAccount || !$this->expenseAccount) {
            $this->markTestSkipped('Required COA accounts not found. Ensure CompanyObserver seeds income and expense accounts.');
        }
    }

    private function postRevenue(string $date, float $amount): void
    {
        $je = JournalEntry::create([
            'company_id'  => $this->company->id,
            'date'        => $date,
            'description' => "Revenue entry",
            'is_posted'   => true,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->cashAccount->id,   'debit' => $amount, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->incomeAccount->id, 'debit' => 0, 'credit' => $amount]);
    }

    private function postExpense(string $date, float $amount): void
    {
        $je = JournalEntry::create([
            'company_id'  => $this->company->id,
            'date'        => $date,
            'description' => "Expense entry",
            'is_posted'   => true,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->expenseAccount->id, 'debit' => $amount, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->cashAccount->id,    'debit' => 0, 'credit' => $amount]);
    }

    /** @test */
    public function net_profit_is_revenue_minus_expenses(): void
    {
        $this->postRevenue('2026-02-05', 5000);
        $this->postExpense('2026-02-10', 2000);

        $result = $this->service->getProfitAndLoss(
            $this->company->id,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $this->assertEquals(5000.0, $result['total_income']);
        $this->assertEquals(2000.0, $result['total_expenses']);
        $this->assertEquals(3000.0, $result['net_profit']);
    }

    /** @test */
    public function multiple_revenue_entries_sum_correctly(): void
    {
        $this->postRevenue('2026-02-01', 1000);
        $this->postRevenue('2026-02-10', 2000);
        $this->postRevenue('2026-02-20', 3000);

        $result = $this->service->getProfitAndLoss(
            $this->company->id,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $this->assertEquals(6000.0, $result['total_income'], 'Three revenue entries should sum to 6000.');
    }

    /** @test */
    public function multiple_expense_categories_sum_correctly(): void
    {
        $this->postRevenue('2026-02-01', 10000);
        $this->postExpense('2026-02-05', 1200);
        $this->postExpense('2026-02-12', 800);
        $this->postExpense('2026-02-18', 500);

        $result = $this->service->getProfitAndLoss(
            $this->company->id,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $this->assertEquals(2500.0, $result['total_expenses'], 'Three expense entries should sum to 2500.');
        $this->assertEquals(7500.0, $result['net_profit']);
    }

    /** @test */
    public function out_of_period_entries_are_excluded(): void
    {
        $this->postRevenue('2026-01-15', 9999); // January — outside period
        $this->postRevenue('2026-02-10', 1000); // February — inside period

        $result = $this->service->getProfitAndLoss(
            $this->company->id,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $this->assertEquals(
            1000.0,
            $result['total_income'],
            'January entry must be excluded from February P&L.'
        );
    }
}
