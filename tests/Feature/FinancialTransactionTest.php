<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Company;
use App\Models\Account;
use App\Models\FinancialTransaction;
use App\Models\JournalEntry;
use App\Events\RentalCompleted;
use App\Services\RentalStateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FinancialTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $rental;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();

        // Seed necessary accounts for the company
        $this->seedAccounts($this->company->id);

        // Create Pricing Policy so calculator doesn't return 0
        $policy = \App\Models\PricingPolicy::create([
            'company_id' => $this->company->id,
            'name' => 'Standard Daily',
            'rental_type' => 'daily',
            'is_default' => true,
        ]);

        \App\Models\PricingRule::create([
            'pricing_policy_id' => $policy->id,
            'rule_type' => 'base_rate',
            'value' => 250, // 250 per day * 4 days = 1000
        ]);

        $this->rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'dispatched',
            'rental_type' => 'daily',
            'start_datetime' => now()->subDays(5),
            'end_datetime' => now()->addDays(5),
            'actual_start_datetime' => now()->subDays(4), // For calculation: 4 days from now()
            'total_amount' => 1000,
            'tax_amount' => 50,
            'grand_total' => 1050,
        ]);
    }

    protected function seedAccounts($companyId)
    {
        Account::withoutGlobalScope('company')->firstOrCreate(
            ['company_id' => $companyId, 'account_code' => '1013'],
            [
                'account_name' => 'Accounts Receivable',
                'account_type' => 'asset',
                'status' => 'active',
            ]
        );

        Account::firstOrCreate(
            ['company_id' => $companyId, 'account_code' => '4010'],
            [
                'account_name' => 'Rental Income',
                'account_type' => 'revenue',
                'status' => 'active',
            ]
        );

        Account::firstOrCreate(
            ['company_id' => $companyId, 'account_code' => '2013'],
            [
                'account_name' => 'VAT Payable',
                'account_type' => 'liability',
                'status' => 'active',
            ]
        );
    }

    #[Test]
    public function rental_completed_event_is_dispatched()
    {
        Event::fake();

        $service = app(RentalStateService::class);
        $service->transition($this->rental, 'completed');

        Event::assertDispatched(RentalCompleted::class, function ($event) {
            return $event->rental->id === $this->rental->id;
        });
    }

    #[Test]
    public function financial_transaction_is_balanced_and_accurate()
    {
        // Actually trigger the listener (not faking events here)
        $service = app(RentalStateService::class);
        $service->transition($this->rental, 'completed');

        $transaction = FinancialTransaction::where('reference_type', 'App\Models\Rental')
            ->where('reference_id', $this->rental->id)
            ->first();

        $this->assertNotNull($transaction, "Financial transaction should be created");

        // Assert Balanced Journal Entries
        $debits = $transaction->journalEntries->sum('debit');
        $credits = $transaction->journalEntries->sum('credit');

        $this->assertEquals($debits, $credits, "Journal entries must be balanced (Debits == Credits)");
        $this->assertEquals(1050, $debits, "Total amount should match grand_total");

        // Assert Account Mapping by Code
        $arEntry = $transaction->journalEntries->filter(fn($e) => $e->account->account_code === '1013')->first();
        $incomeEntry = $transaction->journalEntries->filter(fn($e) => $e->account->account_code === '4010')->first();
        $vatEntry = $transaction->journalEntries->filter(fn($e) => $e->account->account_code === '2013')->first();

        $this->assertNotNull($arEntry, "AR entry missing");
        $this->assertNotNull($incomeEntry, "Income entry missing");
        $this->assertNotNull($vatEntry, "VAT entry missing");

        $this->assertEquals(1050, $arEntry->debit);
        $this->assertEquals(1000, $incomeEntry->credit);
        $this->assertEquals(50, $vatEntry->credit);
    }
}
