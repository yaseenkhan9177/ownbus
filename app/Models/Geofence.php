<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    protected $connection = 'tenant';
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        // 'area' is not directly fillable via array due to geometry type without custom casting
    ];

    /**
     * Scope a query to find geofences containing a specific coordinate.
     */
    public function scopeContainsCoordinate($query, $latitude, $longitude)
    {
        if (config('database.default') === 'sqlite' || config('database.default') === 'sqlite_testing') {
            return $query->whereRaw('1 = 0'); // SQLite doesn't support spatial ST_Contains out of the box
        }

        // MySQL expects POINT(longitude latitude)
        return $query->whereRaw("ST_Contains(area, ST_GeomFromText('POINT(? ?)', 4326))", [$longitude, $latitude]);
    }
}
