<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Company;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Permissions
        $permissions = [
            'view_financial_reports',
            'view_fleet_analytics',
            'view_activity_logs',
        ];

        $permIds = [];
        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm]);
            $permIds[$perm] = $p->id;
        }

        // 2. Assign to Roles
        // We need a company context for Roles usually, but if 'is_system' exists, we can use that.
        // Or just create for a default company ID if strictly required. 
        // Let's assume we create these roles for the first available company or as system roles if supported.
        // Based on Role model: company_id is fillable. 
        // Let's check if we have a company.
        $company = Company::first();
        if (!$company) return; // No company to assign roles to

        // Admin
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'company_id' => $company->id],
            ['description' => 'Admin']
        );
        $admin->permissions()->sync(array_values($permIds));

        // Manager
        $manager = Role::firstOrCreate(
            ['name' => 'manager', 'company_id' => $company->id],
            ['description' => 'Manager']
        );
        if (isset($permIds['view_fleet_analytics'])) {
            $manager->permissions()->sync([$permIds['view_fleet_analytics']]);
        }

        // Accountant
        $accountant = Role::firstOrCreate(
            ['name' => 'accountant', 'company_id' => $company->id],
            ['description' => 'Accountant']
        );
        if (isset($permIds['view_financial_reports'])) {
            $accountant->permissions()->sync([$permIds['view_financial_reports']]);
        }

        // Branch Manager
        $branchManager = Role::firstOrCreate(
            ['name' => 'branch_manager', 'company_id' => $company->id],
            ['description' => 'Branch Manager']
        );
        $bmPerms = [];
        if (isset($permIds['view_fleet_analytics'])) $bmPerms[] = $permIds['view_fleet_analytics'];
        if (isset($permIds['view_financial_reports'])) $bmPerms[] = $permIds['view_financial_reports'];
        $branchManager->permissions()->sync($bmPerms);
    }
}
