<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\PricingPolicy;
use App\Models\PricingRule;
use App\Models\DynamicPricingRule;
use App\Models\Coupon;
use App\Services\RentalPriceCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DynamicPricingTest extends TestCase
{
    use RefreshDatabase;

    protected $calculator;
    protected $company;
    protected $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = app(RentalPriceCalculator::class);
        $this->company = Company::factory()->create();

        // Setup base policy
        $this->policy = PricingPolicy::create([
            'company_id' => $this->company->id,
            'name' => 'Standard Policy',
            'rental_type' => 'daily',
            'is_default' => true
        ]);

        PricingRule::create(['pricing_policy_id' => $this->policy->id, 'rule_type' => 'base_rate', 'value' => 100.0]);
        PricingRule::create(['pricing_policy_id' => $this->policy->id, 'rule_type' => 'daily_km_limit', 'value' => 200.0]);
        PricingRule::create(['pricing_policy_id' => $this->policy->id, 'rule_type' => 'extra_km_rate', 'value' => 2.0]);
    }

    #[Test]
    public function base_calculation_works()
    {
        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'rental_type' => 'daily',
            'start_datetime' => now()->startOfDay(),
            'end_datetime' => now()->startOfDay()->addDays(2), // 2 days
        ]);

        $result = $this->calculator->calculate($rental);

        $this->assertEquals(200.0, $result->base_amount); // 100 * 2
        $this->assertEquals(210.0, $result->grand_total); // 200 + 5% VAT
    }

    #[Test]
    public function seasonal_adjustment_is_applied()
    {
        DynamicPricingRule::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Winter Surge',
            'rule_type' => 'seasonal',
            'conditions' => [
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
            ],
            'adjustment_type' => 'percentage',
            'adjustment_value' => 10.0,
            'priority' => 1
        ]);

        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'rental_type' => 'daily',
            'start_datetime' => now(),
            'end_datetime' => now()->addDay(),
        ]);

        $result = $this->calculator->calculate($rental);

        $this->assertEquals(100.0, $result->base_amount);
        $this->assertCount(1, $result->adjustments);
        $this->assertEquals(10.0, $result->adjustments[0]->calculated_amount); // 10% of 100
        $this->assertEquals(115.5, $result->grand_total); // (100 + 10) * 1.05
    }

    #[Test]
    public function vip_discount_is_applied()
    {
        $vipCustomer = Customer::factory()->create(['company_id' => $this->company->id, 'type' => 'vip']);

        DynamicPricingRule::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'VIP Discount',
            'rule_type' => 'vip',
            'adjustment_type' => 'fixed',
            'adjustment_value' => -50.0,
            'priority' => 1
        ]);

        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $vipCustomer->id,
            'rental_type' => 'daily',
            'start_datetime' => now()->startOfDay(),
            'end_datetime' => now()->startOfDay()->addDays(2),
        ]);

        $result = $this->calculator->calculate($rental);

        $this->assertEquals(200.0, $result->base_amount);
        $this->assertEquals(-50.0, $result->adjustments[0]->calculated_amount);
        $this->assertEquals(157.5, $result->grand_total); // (200 - 50) * 1.05
    }

    #[Test]
    public function long_term_tier_discount_is_applied()
    {
        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'rental_type' => 'daily',
            'start_datetime' => now()->startOfDay(),
            'end_datetime' => now()->startOfDay()->addDays(10), // 10 days
        ]);

        $result = $this->calculator->calculate($rental);

        $this->assertEquals(1000.0, $result->base_amount); // 100 * 10
        // Find the long-term adjustment
        $adjustment = collect($result->adjustments)->firstWhere('type', 'duration');
        $this->assertNotNull($adjustment);
        $this->assertEquals(-100.0, $adjustment->calculated_amount); // -10% of 1000
    }

    #[Test]
    public function coupon_is_applied()
    {
        $coupon = Coupon::factory()->create([
            'company_id' => $this->company->id,
            'code' => 'SAVE50',
            'type' => 'fixed',
            'value' => 50.0
        ]);

        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'rental_type' => 'daily',
            'start_datetime' => now()->startOfDay(),
            'end_datetime' => now()->startOfDay()->addDay(),
            'coupon_id' => $coupon->id
        ]);

        $result = $this->calculator->calculate($rental);

        $this->assertEquals(100.0, $result->base_amount);
        $adjustment = collect($result->adjustments)->firstWhere('type', 'coupon');
        $this->assertNotNull($adjustment);
        $this->assertEquals(-50.0, $adjustment->calculated_amount);
    }

    #[Test]
    public function surge_pricing_is_applied_at_high_utilization()
    {
        // 5 buses total
        Vehicle::factory()->count(4)->create(['company_id' => $this->company->id]);
        $bus = Vehicle::factory()->create(['company_id' => $this->company->id]);

        // Create 4 rentals that overlap with our test rental (80% utilization)
        Rental::factory()->count(4)->create([
            'company_id' => $this->company->id,
            'status' => 'dispatched',
            'start_datetime' => now()->subDay(),
            'end_datetime' => now()->addDay(),
            'bus_id' => Vehicle::factory() // Unique buses
        ]);

        $rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'rental_type' => 'daily',
            'start_datetime' => now(),
            'end_datetime' => now()->addDay(),
            'bus_id' => $bus->id
        ]);

        $result = $this->calculator->calculate($rental);

        $adjustment = collect($result->adjustments)->firstWhere('type', 'surge');
        $this->assertNotNull($adjustment);
        $this->assertEquals(20.0, $adjustment->calculated_amount); // 20% of 100
    }
}
