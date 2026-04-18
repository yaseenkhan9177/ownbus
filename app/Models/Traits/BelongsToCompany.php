<?php

namespace App\Models\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * The current company ID context.
     * @var int|null
     */
    public static $currentCompanyId = null;

    protected static function bootBelongsToCompany()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            // Only apply scope if context is set (which happens AFTER auth is fully loaded)
            // This prevents infinite recursion during User model loading by Auth
            if (static::$currentCompanyId) {
                $builder->where($builder->getQuery()->from . '.company_id', static::$currentCompanyId);
            } elseif (
                !$builder->getModel() instanceof \App\Models\User &&
                Auth::check()
            ) {
                // Fallback for authenticated users who belong to a company
                // This ensures API requests without middleware still get scoped
                $user = Auth::user();
                if ($user->company_id && $user->role !== 'super_admin') {
                    $builder->where($builder->getQuery()->from . '.company_id', $user->company_id);
                }
            }
        });

        static::creating(function ($model) {
            // For creating, we can try to use the context, or fallback to Auth if context not set (e.g. CLI/Tinker)
            // But we must be careful with Auth recursion here too.
            // Usually creating happens after auth, so context should be set.

            if (static::$currentCompanyId && !$model->company_id) {
                $model->company_id = static::$currentCompanyId;
            } elseif (Auth::check()) {
                // Fallback for when middleware hasn't run (e.g. console), but be careful.
                // We use the safer check we implemented before.
                $user = Auth::user();
                $companyId = ($user instanceof \Illuminate\Database\Eloquent\Model) ? $user->getAttribute('company_id') : ($user->company_id ?? null);
                if ($companyId && !$model->company_id) {
                    $model->company_id = $companyId;
                }
            }
        });
    }

    public function scopeWithoutCompany(Builder $builder)
    {
        return $builder->withoutGlobalScope('company');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
