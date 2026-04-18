<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantService;
use Symfony\Component\HttpFoundation\Response;

class SwitchTenantDatabase
{
    /**
     * Handle an incoming request.
     * If the authenticated user has a company, switch the DB connection to
     * the tenant's isolated database so all subsequent queries go there.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = null;

        if (Auth::check()) {
            $user = Auth::user();
            // Regular users use their assigned company_id
            if ($user->role !== 'super_admin') {
                $companyId = $user->company_id;
            } else {
                // Super Admins can 'masquerade' or view a tenant via session if set (e.g. during testing)
                $companyId = $request->session()->get('company_id');
            }
        }

        // If still no companyId (Guest or Super Admin without session), check session
        if (!$companyId) {
            $companyId = $request->session()->get('company_id');
        }

        // Fallback for public routes/guests: Use the first active company if no context is found
        // This prevents "No database selected" errors on public catalogs and quote searches.
        if (!$companyId) {
            $companyId = \App\Models\Company::where('status', 'active')->first()?->id;
        }

        if ($companyId) {
            $company = \App\Models\Company::find($companyId);
            if ($company && $company->database_name) {
                TenantService::switchDatabase($company->database_name);
            }
        }

        return $next($request);
    }
}
