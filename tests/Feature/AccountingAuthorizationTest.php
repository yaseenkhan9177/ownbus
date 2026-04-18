<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'trial_days' => 30, 'is_active' => true, 'features' => []]
        );

        $this->company = Company::factory()->create();
    }

    /**
     * Test company_admin access.
     */
    public function test_company_admin_has_full_accounting_access()
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'company_admin'
        ]);

        $this->actingAs($user);

        $this->get(route('company.accounting.coa'))->assertStatus(200);
        $this->get(route('company.accounting.journals'))->assertStatus(200);
        $this->get(route('company.accounting.reports.index'))->assertStatus(200);
    }

    /**
     * Test staff without permission gets 403.
     */
    public function test_staff_without_permission_is_blocked()
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'staff'
        ]);

        $this->actingAs($user);

        $this->get(route('company.accounting.coa'))->assertStatus(403);
        $this->get(route('company.accounting.journals'))->assertStatus(403);
        $this->get(route('company.accounting.reports.index'))->assertStatus(403);
    }

    /**
     * Test legacy report access for company_admin.
     */
    public function test_legacy_report_access_for_owner()
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'company_admin'
        ]);

        $this->actingAs($user);

        // This was the 403 route mentioned by the user
        $this->get(route('company.reports.index'))->assertStatus(200);
    }
}
