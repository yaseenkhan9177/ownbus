<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DriverPinAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('driver_id')) {
            return redirect()->route('driver.login')
                ->with('error', 'Please log in to continue.');
        }

        return $next($request);
    }
}
