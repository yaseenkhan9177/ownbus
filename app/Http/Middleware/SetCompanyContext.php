<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Traits\BelongsToCompany;

class SetCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Set the company scope for BelongsToCompany trait
            // Note: static property access on the class itself
            BelongsToCompany::$currentCompanyId = $user->company_id;

            // ⚠️ CRITICAL: Tell Spatie which team (company) to use for role checks.
            // Without this, hasRole() always returns false in teams mode.
            if ($user->company_id) {
                setPermissionsTeamId($user->company_id);

                // Self-healing: If user is database 'company_admin', ensure they have Spatie 'Owner' role in this team
                if ($user->role === 'company_admin' && !$user->hasRole('Owner')) {
                    // Ensure the role exists first (it should, but safety first in tenant DB)
                    \Spatie\Permission\Models\Role::firstOrCreate([
                        'name' => 'Owner',
                        'company_id' => $user->company_id
                    ]);
                    $user->assignRole('Owner');
                }
            }
        }

        return $next($request);
    }
}
