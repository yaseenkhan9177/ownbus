<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is a customer (not admin/staff)
        if (!auth()->check()) {
            return redirect()->route('portal.login')
                ->with('error', 'Please login to access your account.');
        }

        $user = auth()->user();
        $role = strtolower($user->role ?? '');

        // Super Admins can access the portal IF they have a company context in session
        if ($role === 'super_admin') {
            if ($request->session()->has('company_id')) {
                return $next($request);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Please select a company to impersonate or access its portal.');
        }

        // Ensure user has 'customer' role
        if ($role !== 'customer') {
            return redirect()->route('company.dashboard')
                ->with('error', 'This area is for customers only.');
        }

        return $next($request);
    }
}
