<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DriverSessionController extends Controller
{
    public function showLogin()
    {
        if (session()->has('driver_id')) {
            return redirect()->route('driver.dashboard');
        }
        return view('driver.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'pin'   => 'required|digits:4',
        ]);

        $driver = Driver::where('phone', $request->phone)
            ->where('status', Driver::STATUS_ACTIVE)
            ->first();

        if (!$driver || !$driver->pin_hash || !Hash::check($request->pin, $driver->pin_hash)) {
            return back()->withErrors(['pin' => 'Invalid phone number or PIN.'])->withInput(['phone' => $request->phone]);
        }

        // Store driver in session
        session([
            'driver_id'         => $driver->id,
            'driver_name'       => $driver->name,
            'driver_company_id' => $driver->company_id,
        ]);

        $driver->update(['last_login_at' => now()]);

        return redirect()->route('driver.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['driver_id', 'driver_name', 'driver_company_id']);
        return redirect()->route('driver.login');
    }
}
