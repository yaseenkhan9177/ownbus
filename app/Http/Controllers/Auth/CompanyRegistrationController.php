<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class CompanyRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register-company');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'image', 'max:2048'], // 2MB Max
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'agreed_to_terms' => ['accepted'],
        ]);

        // Build unique slug for the tenant database name
        $databaseName = 'tenant_' . Str::slug($request->company_name, '_');

        DB::transaction(function () use ($request, $databaseName) {
            // 1. Handle File Upload
            $logoPath = $request->file('logo')->store('logos', 'public');

            // 2. Create Company Record (with database_name stored)
            $company = Company::create([
                'name' => $request->company_name,
                'logo_path' => $logoPath,
                'owner_name' => $request->owner_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'trn_number' => $request->trn_number,
                'status' => 'pending',
                'agreed_to_terms' => true,
                'database_name' => $databaseName,
            ]);

            // 3. Create the physical MySQL tenant database and run migrations
            TenantService::createDatabase($databaseName);
            TenantService::migrateDatabase($databaseName);

            // 4. Create Central User mapped to this company_id
            $user = User::create([
                'name' => $request->owner_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => $company->id,
                'role' => 'company_admin',
            ]);
        });

        return redirect()->route('register.company.pending')->with('sweet_alert', [
            'type' => 'success',
            'title' => 'Registration Successful!',
            'text' => 'Your company account has been created and is pending approval. You will be notified once approved.'
        ]);
    }

    public function pending()
    {
        return view('auth.pending-approval');
    }
}
