<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        if (User::where('email', 'admin@ownbuses.com')->exists()) {
            $this->command->info('Super Admin user already exists.');
            return;
        }

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@ownbuses.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'company_id' => null, // Super admin might not belong to a specific company
        ]);

        $this->command->info('Super Admin user created successfully.');
        $this->command->info('Email: admin@ownbuses.com');
        $this->command->info('Password: password');
    }
}
