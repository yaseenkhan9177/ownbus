<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageMetric extends Model
{
    protected $connection = 'mysql';

    use HasFactory;

    protected $fillable = [
        'company_id',
        'metric_type',
        'current_count',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the latest metric for a company and type.
     */
    public static function getLatest(int $companyId, string $metricType): ?int
    {
        return static::where('company_id', $companyId)
            ->where('metric_type', $metricType)
            ->latest('recorded_at')
            ->value('current_count');
    }
}
