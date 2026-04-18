<?php

namespace App\Services\Intelligence;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\MaintenanceRecord;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class RiskActionDispatcher
{
    public function dispatchVehicleActions(Vehicle $vehicle, array $prediction)
    {
        if ($prediction['risk_score'] >= 75) {
            $this->createPreventiveMaintenance($vehicle, $prediction);
            $this->notifyBranchManager($vehicle, $prediction);
        }
    }

    public function dispatchDriverActions(Driver $driver, array $prediction)
    {
        if ($prediction['risk_score'] >= 80) {
            $this->scheduleSafetyTraining($driver, $prediction);
            $this->notifyOperations($driver, $prediction);
        }
    }

    protected function createPreventiveMaintenance(Vehicle $vehicle, array $prediction)
    {
        // Avoid duplicate preventive tasks
        $exists = MaintenanceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', 'scheduled')
            ->where('description', 'LIKE', '%[PREDICTIVE-AI]%')
            ->exists();

        if (!$exists) {
            MaintenanceRecord::create([
                'vehicle_id' => $vehicle->id,
                'type' => 'preventive',
                'status' => 'scheduled',
                'scheduled_date' => now()->addDays(3),
                'description' => "[PREDICTIVE-AI] Auto-scheduled due to high breakdown probability (" . ($prediction['risk_score']) . "%)",
                'estimated_cost' => 500,
            ]);
            
            Log::info("Automated preventive maintenance scheduled for vehicle {$vehicle->vehicle_number}");
        }
    }

    protected function notifyBranchManager(Vehicle $vehicle, array $prediction)
    {
        // Integration with existing notification system
        // Mocking notification creation
        Log::warning("ALERT: High Breakdown Risk for Vehicle {$vehicle->vehicle_number} in branch {$vehicle->branch_id}");
    }

    protected function scheduleSafetyTraining(Driver $driver, array $prediction)
    {
        // Logic to flag driver for training
        Log::info("Driver {$driver->full_name} flagged for Mandatory Safety Training due to risk score {$prediction['risk_score']}");
    }

    protected function notifyOperations(Driver $driver, array $prediction)
    {
        Log::error("CRITICAL: High Accident Risk for Driver {$driver->full_name}");
    }
}
