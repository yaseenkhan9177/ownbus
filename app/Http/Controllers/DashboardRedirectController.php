<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        // If user has a branch_id and is not the main account holder, they are likely a manager
        if ($user->branch_id) {
            return redirect()->route('company.dashboard', ['view' => 'manager']);
        }

        return redirect()->route('company.dashboard');
    }
}
