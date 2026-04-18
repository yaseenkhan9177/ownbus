<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TenantService
{
    /**
     * Set the current tenant database connection dynamically.
     */
    public static function switchDatabase(string $databaseName): void
    {
        config(['database.connections.tenant.database' => $databaseName]);
        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    /**
     * Create a new database for a tenant.
     */
    public static function createDatabase(string $databaseName): bool
    {
        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to create tenant database {$databaseName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Run tenant migrations on the specified database.
     */
    public static function migrateDatabase(string $databaseName): bool
    {
        try {
            self::switchDatabase($databaseName);

            $exitCode = Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            $output = Artisan::output();
            Log::info("Migration output for {$databaseName}: " . $output);

            if ($exitCode !== 0) {
                throw new \Exception("Migration failed with exit code {$exitCode}. Output: " . $output);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to migrate tenant database {$databaseName}: " . $e->getMessage());
            throw $e; // Rethrow to let RegistrationController handle it
        }
    }

    /**
     * Clean up / drop a tenant database (for testing/deletion).
     */
    public static function dropDatabase(string $databaseName): void
    {
        try {
            DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
        } catch (\Exception $e) {
            Log::error("Failed to drop tenant database {$databaseName}: " . $e->getMessage());
        }
    }
}
