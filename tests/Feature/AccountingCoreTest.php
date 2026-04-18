<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AccountingPeriod;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\SubscriptionPlan;
use App\Services\AccountingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingCoreTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $accounting;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed subscription plans (REQUIRED by CompanyObserver)
        SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Plan',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'trial_days' => 30,
                'is_active' => true,
                'features' => []
            ]
        );

        $this->company = Company::factory()->create();
        $this->accounting = new AccountingService();

        // COA is auto-seeded by CompanyObserver
    }

    /**
     * Test DR=CR Integrity.
     */
    public function test_journal_fails_if_debit_not_equal_credit()
    {
        $cash = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();
        $income = Account::where('company_id', $this->company->id)->where('account_code', '4010')->first();

        if (!$cash || !$income) {
            $this->fail('Required accounts (1011 or 4010) not found in COA.');
        }

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Accounting mismatch");

        $this->accounting->createJournalEntry([
            'company_id' => $this->company->id,
            'date' => now()->toDateString(),
            'description' => 'Unbalanced Entry',
        ], [
            ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $income->id, 'debit' => 0, 'credit' => 99.99], // Mismatch
        ]);
    }

    /**
     * Test Fiscal Period Enforcement.
     */
    public function test_cannot_post_to_closed_fiscal_period()
    {
        AccountingPeriod::create([
            'company_id' => $this->company->id,
            'name' => 'Closed Jan',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-31',
            'is_closed' => true,
        ]);

        $cash = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();
        $income = Account::where('company_id', $this->company->id)->where('account_code', '4010')->first();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("closed accounting period");

        $this->accounting->createJournalEntry([
            'company_id' => $this->company->id,
            'date' => '2025-01-15', // Falls into closed period
            'description' => 'Late Entry',
        ], [
            ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $income->id, 'debit' => 0, 'credit' => 100],
        ]);
    }

    /**
     * Test Leaf Account Rule.
     */
    public function test_cannot_post_to_parent_account()
    {
        $parentAssets = Account::where('company_id', $this->company->id)->where('account_code', '1000')->first(); // Assets (Parent)
        $income = Account::where('company_id', $this->company->id)->where('account_code', '4010')->first();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot post to parent account");

        $this->accounting->createJournalEntry([
            'company_id' => $this->company->id,
            'date' => now()->toDateString(),
            'description' => 'Posting to Parent',
        ], [
            ['account_id' => $parentAssets->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $income->id, 'debit' => 0, 'credit' => 100],
        ]);
    }

    /**
     * Test Automated Journaling on Rental Activation.
     */
    public function test_rental_activation_triggers_journal_entry()
    {
        $user = User::factory()->create(['company_id' => $this->company->id]);
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);
        $vehicle = Vehicle::factory()->create(['company_id' => $this->company->id]);

        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'final_amount' => 500,
        ]);

        // Act: Change status to active (Triggering Observer)
        $this->actingAs($user);
        $rental->update(['status' => 'active']);

        // Assert: Journal Entry Header exists
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'App\Models\Rental',
            'reference_id' => $rental->id,
            'is_posted' => true,
        ]);

        // Assert: Double-entry integrity
        $journal = \App\Models\JournalEntry::where('reference_id', $rental->id)->first();
        $this->assertEquals(500, $journal->lines->sum('debit'));
        $this->assertEquals(500, $journal->lines->sum('credit'));
    }
}
