<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Request;

class AuthLogger
{
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
        ];
    }

    public function handleLogin(Login $event): void
    {
        // Super Admins have no tenant DB — skip activity logging
        if ($event->user->role === 'super_admin' || !$event->user->company_id) {
            return;
        }

        try {
            ActivityLog::create([
                'company_id' => $event->user->company_id,
                'user_id'    => $event->user->id,
                'event'      => 'login',
                'description' => 'User logged in',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Fail silently — logging should never block login
        }
    }

    public function handleLogout(Logout $event): void
    {
        if (!$event->user) return;

        // Super Admins have no tenant DB — skip activity logging
        if ($event->user->role === 'super_admin' || !$event->user->company_id) {
            return;
        }

        try {
            ActivityLog::create([
                'company_id' => $event->user->company_id,
                'user_id'    => $event->user->id,
                'event'      => 'logout',
                'description' => 'User logged out',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Fail silently
        }
    }
}
