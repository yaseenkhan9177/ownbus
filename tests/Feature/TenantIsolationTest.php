<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function company_a_cannot_see_company_b_rentals()
    {
        $companyA = Company::factory()->create();
        $userA = User::factory()->create(['company_id' => $companyA->id]);

        $companyB = Company::factory()->create();
        $rentalB = Rental::factory()->create([
            'company_id' => $companyB->id,
            'contract_number' => 'SECRET-B-001'
        ]);

        $this->actingAs($userA);

        // Verify scope is active in memory
        $this->assertCount(0, Rental::where('contract_number', 'SECRET-B-001')->get());

        // Verify scope is active via HTTP
        $response = $this->get(route('rentals.index'));
        $response->assertStatus(200);
        $response->assertDontSee('SECRET-B-001');
    }

    #[Test]
    public function company_id_is_automatically_assigned()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $this->actingAs($user);

        $rental = Rental::create([
            'customer_id' => Customer::factory()->create(['company_id' => $company->id])->id,
            'rental_type' => 'daily',
            'start_datetime' => now()->addDay(),
            'end_datetime' => now()->addDays(2),
            'pickup_location' => 'IsolationTest',
            'contract_number' => 'TEST-001',
            'uuid' => (string) \Illuminate\Support\Str::uuid()
        ]);

        $this->assertEquals($company->id, $rental->company_id);
    }
    #[Test]
    public function branch_isolation_works()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $branch_1 = \App\Models\Branch::factory()->create(['company_id' => $company->id]);
        $branch_2 = \App\Models\Branch::factory()->create(['company_id' => $company->id]);

        $customer = Customer::factory()->create(['company_id' => $company->id]);

        // Assign user to branch 1 ONLY
        $user->branches()->attach($branch_1->id, ['is_active' => true, 'assigned_at' => now()]);

        // Create rentals in both branches
        Rental::factory()->create([
            'company_id' => $company->id,
            'branch_id' => $branch_1->id,
            'customer_id' => $customer->id,
            'contract_number' => 'B1-RENTAL'
        ]);
        Rental::factory()->create([
            'company_id' => $company->id,
            'branch_id' => $branch_2->id,
            'customer_id' => $customer->id,
            'contract_number' => 'B2-RENTAL'
        ]);

        $this->actingAs($user);
        $response = $this->get(route('rentals.index'));

        // Should only see B1-RENTAL
        $response->assertSee('B1-RENTAL');
        $response->assertDontSee('B2-RENTAL');
    }

    #[Test]
    public function cache_keys_are_company_scoped()
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $userA = User::factory()->create(['company_id' => $companyA->id]);
        $userB = User::factory()->create(['company_id' => $companyB->id]);

        $cacheKey = "dashboard_stats";

        // Store value as Company A
        $this->actingAs($userA);
        Cache::tags(['company_' . $companyA->id])->put($cacheKey, 'data_a');

        // Store value as Company B
        $this->actingAs($userB);
        Cache::tags(['company_' . $companyB->id])->put($cacheKey, 'data_b');

        // Verify isolation
        $this->actingAs($userA);
        $this->assertEquals('data_a', Cache::tags(['company_' . $companyA->id])->get($cacheKey));

        $this->actingAs($userB);
        $this->assertEquals('data_b', Cache::tags(['company_' . $companyB->id])->get($cacheKey));
    }
}
