<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SuperAdminRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuperAdminRequestMail;

class SuperAdminAuthController extends Controller
{
    public function showPinForm()
    {
        return view('auth.super-admin.pin');
    }

    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $masterPin = env('SUPER_ADMIN_MASTER_PIN', '893421'); // Default as requested

        if ($request->pin === $masterPin) {
            $request->session()->put('super_admin_pin_verified', true);
            return redirect()->route('super-admin.register');
        }

        return back()->with('error', 'Invalid Master PIN. Access Denied.');
    }

    public function showRegistrationForm(Request $request)
    {
        if (!$request->session()->has('super_admin_pin_verified')) {
            return redirect()->route('super-admin.pin');
        }

        $allowedIp = env('SUPER_ADMIN_ALLOWED_IP');
        if ($allowedIp && $request->ip() !== $allowedIp) {
            abort(403, 'IP Address not authorized for Super Admin registration.');
        }

        return view('auth.super-admin.register');
    }

    public function register(Request $request)
    {
        if (!$request->session()->has('super_admin_pin_verified')) {
            return redirect()->route('super-admin.pin');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|unique:super_admin_requests',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $superAdminCount = User::where('role', 'super_admin')->count();

        if ($superAdminCount === 0) {
            // Rule 1: First user is auto-approved
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'super_admin',
            ]);

            $request->session()->forget('super_admin_pin_verified');
            auth()->login($user);
            return redirect()->route('admin.dashboard')->with('success', 'Welcome! First Super Admin created successfully.');
        } else {
            // Rule 2: Create Pending Request
            SuperAdminRequest::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'pending',
                'ip_address' => $request->ip(),
            ]);

            // Notify existing super admins
            $superAdmins = User::where('role', 'super_admin')->get();
            foreach ($superAdmins as $admin) {
                Mail::to($admin->email)->send(new SuperAdminRequestMail($request->name, $request->email));
            }

            $request->session()->forget('super_admin_pin_verified');
            return redirect()->route('login')->with('success', 'Registration request submitted. Pending approval from an existing Super Admin.');
        }
    }
}
