<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_scope_is_applied_when_context_set_on_trait()
    {
        // 1. Create two companies without triggering observers
        $companyA = Company::withoutEvents(function () {
            return Company::factory()->create(['name' => 'Company A']);
        });

        $companyB = Company::withoutEvents(function () {
            return Company::factory()->create(['name' => 'Company B']);
        });

        // 2. Create Customers (Vulnerable Model - uses BelongsToCompany but NOT ScopedByCompany)
        $customerA = Customer::factory()->create(['company_id' => $companyA->id, 'name' => 'Cust A']);
        $customerB = Customer::factory()->create(['company_id' => $companyB->id, 'name' => 'Cust B']);

        // 3. Simulate Authenticated User (Context NOT set via middleware)
        // This tests the fallback logic we just added to BelongsToCompany
        $userA = User::factory()->create(['company_id' => $companyA->id]);
        $this->actingAs($userA);

        // We do NOT set static property matching the vulnerability scenario (or API context)
        // \App\Models\Traits\BelongsToCompany::$currentCompanyId = $companyA->id;

        // 4. Query Customers
        // If Model uses BelongsToCompany trait, it should see the static property
        // BUT if traits static properties are distinct per class, it won't see it.
        $customers = Customer::all();

        // 5. Assertions
        $this->assertEquals(1, $customers->count(), 'Tenant isolation failed for Customer! Saw ' . $customers->count());
        $this->assertEquals($customerA->id, $customers->first()->id);
    }
}
