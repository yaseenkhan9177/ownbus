<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Rental;
use App\Models\User;
use App\Services\CustomerBalanceService;
use App\Services\CustomerCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure a subscription plan exists so CompanyObserver doesn't fail
        \App\Models\SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'is_active' => true,
            'features' => []
        ]);

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'company_admin'
        ]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_generates_unique_customer_codes_per_company()
    {
        $customer1 = Customer::factory()->create(['company_id' => $this->company->id]);
        $customer2 = Customer::factory()->create(['company_id' => $this->company->id]);

        $this->assertStringStartsWith('CUS-', $customer1->customer_code);
        $this->assertStringStartsWith('CUS-', $customer2->customer_code);
        $this->assertNotEquals($customer1->customer_code, $customer2->customer_code);
    }

    /** @test */
    public function it_increases_customer_balance_when_rental_becomes_active()
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->company->id,
            'current_balance' => 0
        ]);

        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $customer->id,
            'final_amount' => 500,
            'status' => 'draft'
        ]);

        $this->assertEquals(1, $customer->rentals()->count());
        $this->assertEquals(0, (float) $customer->fresh()->current_balance);

        // Activate rental
        $rental->status = Rental::STATUS_ACTIVE;
        $rental->save();

        $this->assertEquals(500, (float) $customer->fresh()->current_balance);
    }

    /** @test */
    public function customer_risk_level_reflects_financial_status()
    {
        $customer = Customer::factory()->make([
            'status' => Customer::STATUS_ACTIVE,
            'credit_limit' => 1000,
            'current_balance' => 100
        ]);

        $this->assertEquals('green', $customer->risk_level);

        $customer->current_balance = 850; // 85%
        $this->assertEquals('yellow', $customer->risk_level);

        $customer->status = Customer::STATUS_BLACKLISTED;
        $this->assertEquals('red', $customer->risk_level);
    }

    /** @test */
    public function it_blocks_rentals_if_exceeding_credit_limit()
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->company->id,
            'credit_limit' => 500,
            'current_balance' => 450
        ]);

        $balanceService = new CustomerBalanceService();

        // Should block additional 100
        $this->assertFalse($balanceService->canAfford($customer, 100));

        // Should allow additional 40
        $this->assertTrue($balanceService->canAfford($customer, 40));
    }

    /** @test */
    public function it_blocks_rentals_if_limit_is_zero_cash_only()
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->company->id,
            'credit_limit' => 0,
            'current_balance' => 0
        ]);

        $balanceService = new CustomerBalanceService();

        // Should block any amount
        $this->assertFalse($balanceService->canAfford($customer, 1));
    }
}
