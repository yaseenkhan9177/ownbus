<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'view accounting',
            'view reports',
            'view rentals',
            'create rentals',
            'edit rentals',
            'manage customers',
            'manage vehicles',
            'manage drivers',
            'manage maintenance',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // create roles and assign created permissions

        // Owner gets all permissions
        $owner = Role::firstOrCreate(['name' => 'Owner', 'company_id' => getPermissionsTeamId()]);
        $owner->givePermissionTo(Permission::all());

        // Accountant
        $accountant = Role::firstOrCreate(['name' => 'Accountant', 'company_id' => getPermissionsTeamId()]);
        $accountant->givePermissionTo([
            'view accounting',
            'view reports',
            'view rentals'
        ]);

        // Booking Clerk
        $bookingClerk = Role::firstOrCreate(['name' => 'Booking Clerk', 'company_id' => getPermissionsTeamId()]);
        $bookingClerk->givePermissionTo([
            'create rentals',
            'edit rentals',
            'manage customers',
            'view rentals'
        ]);

        // Operations Manager
        $operationsManager = Role::firstOrCreate(['name' => 'Operations Manager', 'company_id' => getPermissionsTeamId()]);
        $operationsManager->givePermissionTo([
            'manage vehicles',
            'manage drivers',
            'manage maintenance'
        ]);

        // Driver
        $driver = Role::firstOrCreate(['name' => 'Driver', 'company_id' => getPermissionsTeamId()]);
        $driver->givePermissionTo([]);
    }
}
