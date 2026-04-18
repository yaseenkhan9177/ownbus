<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopedByCompany
{
    /**
     * Boot the trait to add the global scope.
     */
    public static function bootScopedByCompany()
    {
        static::addGlobalScope('company_scope', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // Super Admins see everything
                if ($user->role === 'super_admin') {
                    return;
                }

                // Others are scoped to their company
                if ($user->company_id) {
                    $builder->where($builder->getQuery()->from . '.company_id', $user->company_id);
                } else {
                    // Prevent access if no company_id is set for non-super_admins
                    $builder->whereRaw('1 = 0');
                }
            }
        });

        static::creating(function ($model) {
            if (Auth::check() && !isset($model->company_id)) {
                $user = Auth::user();
                if ($user->role !== 'super_admin' && $user->company_id) {
                    $model->company_id = $user->company_id;
                }
            }
        });
    }
}
