<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId;
});

Broadcast::channel('company.{companyId}.broadcasts', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId;
});

Broadcast::channel('broadcasts.{role}', function ($user, $role) {
    if ($role === 'all') return true;
    return $user->role === $role || $user->role === 'super_admin';
});
