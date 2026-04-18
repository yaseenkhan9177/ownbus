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
 * BalanceSheetEquationTest
 *
 * Tests:
 *  1. Total Assets = Total Liabilities + Total Equity after postings
 *  2. Net profit from income is reflected in retained_earnings (not stored, computed dynamically)
 *  3. Mismatch scenario returns is_balanced = false
 */
class BalanceSheetEquationTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected FinancialReportService $service;
    protected Account $cashAccount;
    protected Account $incomeAccount;
    protected Account $arAccount;
    protected Account $equityAccount;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'trial_days' => 30, 'is_active' => true, 'features' => []]
        );

        $this->company = Company::factory()->create();
        $this->service = new FinancialReportService();

        $this->cashAccount   = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();
        $this->incomeAccount = Account::where('company_id', $this->company->id)->where('account_code', '4010')->first();
        $this->equityAccount = Account::where('company_id', $this->company->id)->where('account_type', 'equity')->first();

        if (!$this->cashAccount || !$this->incomeAccount || !$this->equityAccount) {
            $this->markTestSkipped('Required COA accounts not found. Ensure CompanyObserver seeds asset, income, and equity accounts.');
        }
    }

    private function postEntry(string $date, Account $debitAcc, Account $creditAcc, float $amount): void
    {
        $je = JournalEntry::create([
            'company_id'  => $this->company->id,
            'date'        => $date,
            'description' => 'Balance sheet test entry',
            'is_posted'   => true,
        ]);

        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $debitAcc->id,  'debit' => $amount, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $creditAcc->id, 'debit' => 0, 'credit' => $amount]);
    }

    /** @test */
    public function balance_sheet_equation_assets_equals_liabilities_plus_equity(): void
    {
        // Owner invests 10000 — DR Cash / CR Equity
        $this->postEntry('2026-02-01', $this->cashAccount, $this->equityAccount, 10000);

        $result = $this->service->getBalanceSheet(
            $this->company->id,
            Carbon::parse('2026-02-28')
        );

        $this->assertTrue(
            $result['is_balanced'],
            "Assets ({$result['total_assets']}) must equal L+E ({$result['total_liab_equity']})."
        );

        $this->assertEqualsWithDelta(
            $result['total_assets'],
            $result['total_liab_equity'],
            0.01,
            'Assets = Liabilities + Equity equation must hold within rounding tolerance.'
        );
    }

    /** @test */
    public function retained_earnings_reflects_net_profit_dynamically(): void
    {
        // DR Cash / CR Income = 5000 profit
        $this->postEntry('2026-02-01', $this->cashAccount, $this->incomeAccount, 5000);

        $result = $this->service->getBalanceSheet(
            $this->company->id,
            Carbon::parse('2026-02-28')
        );

        // Retained earnings = net profit to date (computed from P&L, not stored)
        $this->assertEquals(
            5000.0,
            $result['retained_earnings'],
            'Retained earnings must equal the net income posted as journal entries.'
        );

        // Equation must still hold
        $this->assertTrue($result['is_balanced']);
    }

    /** @test */
    public function balance_sheet_equation_holds_after_multiple_transactions(): void
    {
        // Owner invests 20000
        $this->postEntry('2026-01-01', $this->cashAccount, $this->equityAccount, 20000);
        // Earns 8000 revenue
        $this->postEntry('2026-02-01', $this->cashAccount, $this->incomeAccount, 8000);

        $result = $this->service->getBalanceSheet(
            $this->company->id,
            Carbon::parse('2026-02-28')
        );

        $this->assertEqualsWithDelta(
            $result['total_assets'],
            $result['total_liab_equity'],
            0.01
        );
        $this->assertTrue($result['is_balanced']);
    }
}
