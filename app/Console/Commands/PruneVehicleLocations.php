<?php

namespace App\Console\Commands;

use App\Models\VehicleLocation;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Prune Vehicle Locations Command
 *
 * Keeps the vehicle_locations table lean by deleting records older than
 * the configured retention period (default: 30 days).
 *
 * GPS devices can generate thousands of pings per day per vehicle.
 * Without pruning, this table grows unboundedly.
 *
 * Artisan: php artisan fleet:prune-locations
 * Schedule: daily at 03:00 (registered in routes/console.php)
 */
class PruneVehicleLocations extends Command
{
    protected $signature = 'fleet:prune-locations
                            {--days=30 : Number of days to retain (default: 30)}
                            {--dry-run : Preview row count without deleting}';

    protected $description = 'Delete GPS location records older than N days (keeps vehicle_locations table lean)';

    public function handle(): int
    {
        $days   = (int) $this->option('days');
        $cutoff = Carbon::now()->subDays($days);
        $dryRun = $this->option('dry-run');

        $this->newLine();
        $this->line("  <fg=cyan>GPS Location Pruning</> — cutoff: {$cutoff->toDateString()} (-{$days} days)");
        $this->newLine();

        $count = VehicleLocation::where('recorded_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info('  ✅ No records to prune.');
            return 0;
        }

        if ($dryRun) {
            $this->warn("  DRY RUN — {$count} record(s) would be deleted.");
            return 0;
        }

        $deleted = VehicleLocation::where('recorded_at', '<', $cutoff)->delete();

        $this->info("  ✅ Pruned {$deleted} GPS location record(s) older than {$days} days.");

        return 0;
    }
}
