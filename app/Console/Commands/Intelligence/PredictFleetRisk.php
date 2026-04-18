<?php

namespace App\Console\Commands\Intelligence;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\Intelligence\VehicleRiskPredictionService;
use App\Services\Intelligence\DriverRiskPredictionService;
use App\Services\Intelligence\RiskActionDispatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PredictFleetRisk extends Command
{
    protected $signature = 'fleet:predict-risk';
    protected $description = 'Predict breakdown and accident risks for the entire fleet';

    protected $vehicleService;
    protected $driverService;
    protected $dispatcher;

    public function __construct(
        VehicleRiskPredictionService $vehicleService,
        DriverRiskPredictionService $driverService,
        RiskActionDispatcher $dispatcher
    ) {
        parent::__construct();
        $this->vehicleService = $vehicleService;
        $this->driverService = $driverService;
        $this->dispatcher = $dispatcher;
    }

    public function handle()
    {
        $this->info("Starting fleet risk prediction...");

        // 1. Vehicle Breakdown Predictions
        $vehicles = Vehicle::where('status', '!=', 'sold')->get();
        $this->withProgressBar($vehicles, function ($vehicle) {
            $prediction = $this->vehicleService->predictBreakdownRisk($vehicle);

            DB::connection('tenant')->table('vehicle_risk_predictions')->insert([
                'vehicle_id' => $vehicle->id,
                'risk_score' => $prediction['risk_score'],
                'probability_30_days' => $prediction['probability_next_30_days'],
                'risk_level' => $prediction['risk_level'],
                'signals_json' => json_encode($prediction['signals']),
                'predicted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->dispatcher->dispatchVehicleActions($vehicle, $prediction);
        });

        $this->newLine();
        $this->info("Vehicle predictions complete.");

        // 2. Driver Accident Predictions
        $drivers = Driver::where('status', 'active')->get();
        $this->withProgressBar($drivers, function ($driver) {
            $prediction = $this->driverService->predictAccidentRisk($driver);

            DB::connection('tenant')->table('driver_risk_predictions')->insert([
                'driver_id' => $driver->id,
                'risk_score' => $prediction['risk_score'],
                'probability_60_days' => $prediction['probability_60_days'],
                'risk_level' => $prediction['risk_level'],
                'signals_json' => json_encode($prediction['signals']),
                'predicted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->dispatcher->dispatchDriverActions($driver, $prediction);
        });

        $this->newLine();
        $this->info("Fleet risk prediction complete.");
    }
}
