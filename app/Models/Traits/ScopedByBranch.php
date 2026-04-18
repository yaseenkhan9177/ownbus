<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopedByBranch
{
    /**
     * Boot the trait to add the global scope.
     */
    public static function bootScopedByBranch()
    {
        static::addGlobalScope('branch_scope', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // Super Admins and Company Owners see everything in the company
                if ($user->role === 'super_admin' || $user->role === 'company_admin') {
                    return;
                }

                // Branch Managers are scoped to their branch
                // Assuming branch_id is available on the user if they are a branch manager
                // or we check via a pivot table. For now, simple branch_id check.
                if ($user->role === 'branch_manager' && $user->branch_id) {
                    $builder->where($builder->getQuery()->from . '.branch_id', $user->branch_id);
                }
            }
        });

        static::creating(function ($model) {
            if (Auth::check() && !isset($model->branch_id)) {
                $user = Auth::user();
                if ($user->role === 'branch_manager' && $user->branch_id) {
                    $model->branch_id = $user->branch_id;
                }
            }
        });
    }
}
