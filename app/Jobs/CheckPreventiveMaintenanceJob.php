<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\VehicleServiceInterval;
use App\Models\User;
use App\Notifications\MaintenanceAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckPreventiveMaintenanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $intervals = VehicleServiceInterval::with(['vehicle', 'company'])->get();

        foreach ($intervals as $interval) {
            $vehicle = $interval->vehicle;

            if (!$vehicle || $vehicle->status === 'inactive') {
                continue;
            }

            $alertReason = null;

            // 1. Check Date
            if ($interval->next_due_date) {
                // Calculate days until due. Negative means overdue.
                $daysUntilDue = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($interval->next_due_date)->startOfDay(), false);

                if ($daysUntilDue <= 7 && $daysUntilDue > 0) {
                    $alertReason = "due in {$daysUntilDue} days";
                } elseif ($daysUntilDue <= 0) {
                    $alertReason = "overdue by " . abs($daysUntilDue) . " days";
                }
            }

            // 2. Check Odometer
            // If date didn't trigger, or if odometer takes precedence, we check it.
            if (!$alertReason && $interval->next_due_odometer && $vehicle->current_odometer >= $interval->next_due_odometer) {
                $over = $vehicle->current_odometer - $interval->next_due_odometer;
                $alertReason = $over > 0 ? "overdue by {$over} km" : "due exactly now by km";
            } elseif (!$alertReason && $interval->next_due_odometer && ($interval->next_due_odometer - $vehicle->current_odometer) <= 500) {
                // Warn if within 500km
                $diff = $interval->next_due_odometer - $vehicle->current_odometer;
                $alertReason = "due soon (within {$diff} km)";
            }

            if ($alertReason) {
                $admins = User::where('company_id', $interval->company_id)
                    ->whereIn('role', ['company_admin', 'super_admin'])
                    ->get();

                foreach ($admins as $admin) {
                    $admin->notify(new MaintenanceAlert($vehicle, $interval->service_type, $alertReason));
                }

                Log::info("Maintenance Alert generated for Vehicle {$vehicle->id}: {$alertReason}");
            }
        }
    }
}
