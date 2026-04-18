<?php

namespace App\Services\SaaS;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SystemMonitorService
{
    /**
     * Retrieve all core diagnostic information for the Super Admin system dashboard.
     * Caches heavy DB counts for 5 minutes to prevent aggressive polling.
     */
    public function getDiagnostics(): array
    {
        return [
            'environment' => $this->getEnvironmentInfo(),
            'database' => $this->getDatabaseInfo(),
            'php_config' => $this->getPhpConfig(),
            'queues' => $this->getQueueStatus(),
            'storage' => $this->getStorageInfo(),
        ];
    }

    /**
     * Get basic environment and framework parameters.
     */
    private function getEnvironmentInfo(): array
    {
        return [
            'os' => php_uname('s') . ' ' . php_uname('r') . ' (' . php_uname('m') . ')',
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'timezone' => config('app.timezone'),
        ];
    }

    /**
     * Get Database driver and size (size logic specifically for MySQL/PostgreSQL).
     */
    private function getDatabaseInfo(): array
    {
        $connection = config('database.default');
        $dbName = config("database.connections.{$connection}.database");
        $sizeInMb = 0;

        try {
            if ($connection === 'mysql') {
                $query = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size' 
                                     FROM information_schema.TABLES 
                                     WHERE table_schema = ?", [$dbName]);
                $sizeInMb = $query[0]->size ?? 0;
            } elseif ($connection === 'pgsql') {
                $query = DB::select("SELECT ROUND(pg_database_size(?)/1024/1024, 2) AS size", [$dbName]);
                $sizeInMb = $query[0]->size ?? 0;
            }
        } catch (\Exception $e) {
            $sizeInMb = 'Unknown (Permissions restricted)';
        }

        return [
            'driver' => ucfirst($connection),
            'database_name' => $dbName,
            'size_mb' => $sizeInMb,
        ];
    }

    /**
     * Retrieve critical PHP limits that impact file uploads and heavy queries.
     */
    private function getPhpConfig(): array
    {
        return [
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'extensions' => [
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'openssl' => extension_loaded('openssl'),
                'curl' => extension_loaded('curl'),
                'gd' => extension_loaded('gd'),
                'zip' => extension_loaded('zip'),
            ]
        ];
    }

    /**
     * Safe retrieval of disk free space (can be blocked on shared hosts).
     */
    private function getStorageInfo(): array
    {
        try {
            $path = storage_path();
            $totalBytes = disk_total_space($path);
            $freeBytes = disk_free_space($path);
            $usedBytes = $totalBytes - $freeBytes;

            return [
                'available' => true,
                'total_gb' => round($totalBytes / 1024 / 1024 / 1024, 2),
                'free_gb' => round($freeBytes / 1024 / 1024 / 1024, 2),
                'used_gb' => round($usedBytes / 1024 / 1024 / 1024, 2),
                'usage_percent' => $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 1) : 0,
            ];
        } catch (\Exception $e) {
            return [
                'available' => false,
                'message' => 'Disk functions restricted by host environment.',
            ];
        }
    }

    /**
     * Query pending and failed background jobs.
     */
    private function getQueueStatus(): array
    {
        return Cache::remember('system_queue_status', 300, function () {
            $pending = 0;
            $failed = 0;

            try {
                if (Schema::hasTable('jobs')) {
                    $pending = DB::table('jobs')->count();
                }
                if (Schema::hasTable('failed_jobs')) {
                    $failed = DB::table('failed_jobs')->count();
                }
            } catch (\Exception $e) {
                // Return 0s if DB connections fail or tables don't exist yet
            }

            return [
                'pending_jobs' => $pending,
                'failed_jobs' => $failed,
            ];
        });
    }
}
