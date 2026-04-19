<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AgreementAcceptance;
use App\Models\AgreementVersion;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function showForm()
    {
        $agreement = AgreementVersion::where('active', true)->orderBy('id', 'desc')->first();
        return view('auth.register-wizard', compact('agreement'));
    }

    public function process(Request $request)
    {
        $request->validate([
            // Step 1: Company Info
            'company_name'          => 'required|string|max:255',
            'trade_license_number'  => 'required|string|max:255',
            'trn_number'            => 'nullable|string|max:255',
            'address'               => 'required|string|max:500',
            'country'               => 'required|string|max:255',
            'total_vehicles'        => 'required|integer|min:1',
            'contact_phone'         => 'required|string|max:50',
            'registration_source'   => 'nullable|string|max:255',

            // Step 2: Account Owner
            'owner_name'                  => 'required|string|max:255',
            'owner_email'                 => 'required|string|email|max:255|unique:users,email',
            'owner_password'              => 'required|string|min:8|confirmed',
            'owner_password_confirmation' => 'required|string',

            // Step 3: Plan Selection
            'plan' => 'required|string|in:starter,professional,enterprise',

            // Step 4: Legal Acceptance
            'agree_tos'         => 'required|accepted',
            'agree_data_policy' => 'required|accepted',
            'agreement_version' => 'required|string|exists:agreement_versions,version',
        ]);

        if ($request->total_vehicles > 50 && $request->plan !== 'enterprise') {
            throw ValidationException::withMessages([
                'plan' => 'Enterprise plan is required for fleets above 50 vehicles.'
            ]);
        }

        try {
            // Build tenant DB name from company name slug
            $databaseName = 'tenant_' . Str::slug($request->company_name, '_');

            // ──────────────────────────────────────────────
            // PHASE 1: Central DB — Company, User, Subscription, Agreement
            // ──────────────────────────────────────────────
            DB::beginTransaction();

            $company = Company::create([
                'name'                  => $request->company_name,
                'trade_license_number'  => $request->trade_license_number,
                'trn_number'            => $request->trn_number,
                'address'               => $request->address,
                'country'               => $request->country,
                'total_vehicles'        => $request->total_vehicles,
                'phone'                 => $request->contact_phone,
                'email'                 => $request->owner_email,
                'owner_name'            => $request->owner_name,
                'registration_source'   => $request->registration_source,
                'status'                => 'pending',
                'agreed_to_terms'       => true,
                'database_name'         => $databaseName,
            ]);

            $user = User::create([
                'name'       => $request->owner_name,
                'email'      => $request->owner_email,
                'password'   => Hash::make($request->owner_password),
                'company_id' => $company->id,
                'role'       => 'company_admin',
            ]);

            $planSlug = $request->plan === 'professional' ? 'growth' : $request->plan;
            $subscriptionPlan = \App\Models\SubscriptionPlan::where('slug', $planSlug)
                ->where('is_active', true)
                ->orderBy('version', 'desc')
                ->firstOrFail();

            Subscription::create([
                'company_id'           => $company->id,
                'plan_id'              => $subscriptionPlan->id,
                'plan_version'         => $subscriptionPlan->version,
                'status'               => 'active',
                'current_period_start' => now(),
                'current_period_end'   => now()->addYear(),
            ]);

            $agreement = AgreementVersion::where('version', $request->agreement_version)->firstOrFail();
            AgreementAcceptance::create([
                'company_id'   => $company->id,
                'version'      => $agreement->version,
                'signed_by'    => $user->id,
                'ip_address'   => $request->ip(),
                'content_hash' => hash('sha256', $agreement->content),
                'signed_at'    => now(),
            ]);

            DB::commit();

            // ──────────────────────────────────────────────
            // PHASE 2: Tenant DB — Provision & seed defaults
            // ──────────────────────────────────────────────
            TenantService::createDatabase($databaseName);
            TenantService::migrateDatabase($databaseName);

            // Seed default Head Office Branch on the tenant connection
            DB::connection('tenant')->table('branches')->insert([
                'name'           => 'Head Office',
                'code'           => 'HQ',
                'is_head_office' => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // ⚠️ CRITICAL Phase 2.5: Role & Permission Seeding (Tenant)
            // Tell Spatie which team we're working with
            setPermissionsTeamId($company->id);

            // Seed Roles and Permissions
            $roleSeeder = new \Database\Seeders\RolesAndPermissionsSeeder();
            $roleSeeder->run();

            // Seed Chart of Accounts on the tenant connection
            // Account model uses $connection = 'tenant' by default
            $seeder = new \Database\Seeders\ChartOfAccountsSeeder();
            $seeder->run($company);

            // Assign Owner role to the registration user
            $user->assignRole('Owner');


            session()->flash('sweet_alert', [
                'type'  => 'success',
                'title' => 'Registration Received!',
                'text'  => 'Your workspace has been provisioned and is pending Super Admin approval. You can log in once approved.',
            ]);

            return response()->json([
                'success'      => true,
                'redirect_url' => route('login'),
            ]);
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showAgreement($version)
    {
        $agreement = AgreementVersion::where('version', $version)->firstOrFail();
        return response()->json(['content' => $agreement->content]);
    }
}
