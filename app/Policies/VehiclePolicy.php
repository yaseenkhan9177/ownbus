<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    /**
     * SuperAdmin or Owner bypass — grant all access automatically.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Owners can do everything within their company
        if ($user->company_id && $user->hasRole('Owner')) {
            return true;
        }

        return null; // Fall through to individual methods
    }

    public function viewAny(User $user): bool
    {
        return (bool) $user->company_id && $user->hasAnyRole(['Owner', 'Operations Manager', 'Accountant', 'Booking Clerk']);
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->hasAnyRole(['Owner', 'Operations Manager', 'Accountant', 'Booking Clerk']);
    }

    public function create(User $user): bool
    {
        return (bool) $user->company_id && $user->hasAnyRole(['Owner', 'Operations Manager']);
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->hasAnyRole(['Owner', 'Operations Manager']);
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->hasAnyRole(['Owner', 'Operations Manager']);
    }

    public function restore(User $user, Vehicle $vehicle): bool
    {
        return $user->hasAnyRole(['Owner', 'Operations Manager']);
    }

    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        return false; // Only super admins (handled in before())
    }
}
