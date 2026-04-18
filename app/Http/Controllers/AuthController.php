<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(\Illuminate\Http\Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $user = \Illuminate\Support\Facades\Auth::user();

            // Check if user is a company admin and company is not active
            if ($user->role === 'company_admin' && $user->company && $user->company->status !== 'active') {
                \Illuminate\Support\Facades\Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is currently ' . $user->company->status . '. Please contact support.',
                ])->onlyInput('email');
            }

            if ($user->role === 'super_admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            $request->session()->regenerate();

            // Prevent 403: If intended destination is admin-only, clear it for non-super-admins
            $intended = session()->get('url.intended');
            if ($intended && str_contains($intended, '/admin/')) {
                session()->forget('url.intended');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
