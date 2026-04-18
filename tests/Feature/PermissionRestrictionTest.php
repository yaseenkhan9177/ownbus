<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionRestrictionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_restricts_financial_actions_to_proper_permissions()
    {
        $company = Company::factory()->create();

        // 1. Create a "Staff" role without financial permissions
        $staffRole = Role::create(['company_id' => $company->id, 'name' => 'Staff']);
        $staffUser = User::factory()->create(['company_id' => $company->id]);
        $staffUser->roles()->attach($staffRole);

        // 2. Create an "Accountant" role with permissions
        $accountantRole = Role::create(['company_id' => $company->id, 'name' => 'Accountant']);
        $permission = Permission::where('name', 'close_accounting_period')->first();
        if (!$permission) {
            $permission = Permission::create(['name' => 'close_accounting_period', 'group' => 'Accounting']);
        }
        $accountantRole->permissions()->attach($permission);

        $accountantUser = User::factory()->create(['company_id' => $company->id]);
        $accountantUser->roles()->attach($accountantRole);

        // 3. Verify permissions (using direct model method)
        $this->assertFalse($staffUser->hasPermission('close_accounting_period'));
        $this->assertTrue($accountantUser->hasPermission('close_accounting_period'));
    }
}
