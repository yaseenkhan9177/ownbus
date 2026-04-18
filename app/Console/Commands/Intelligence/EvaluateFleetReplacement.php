<?php

namespace App\Console\Commands\Intelligence;

use Illuminate\Console\Command;

class EvaluateFleetReplacement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fleet:evaluate-replacement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate fleet replacement scores weekly based on performance intelligence';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Intelligence\FleetReplacementService $replacementService)
    {
        $vehicles = \App\Models\Vehicle::where('status', '!=', 'inactive')->get();
        $this->info("Evaluating replacement suitability for {$vehicles->count()} vehicles...");

        foreach ($vehicles as $vehicle) {
            $result = $replacementService->evaluateVehicle($vehicle);

            \Illuminate\Support\Facades\DB::connection('tenant')->table('vehicle_replacement_snapshots')->insert([
                'vehicle_id' => $vehicle->id,
                'replacement_score' => $result['replacement_score'],
                'recommendation' => $result['recommendation'],
                'signals_json' => json_encode($result['signals']),
                'calculated_at' => now(),
            ]);

            if ($result['recommendation'] === 'replace') {
                $this->warn("Vehicle #{$vehicle->vehicle_number} is RECOMMENDED FOR REPLACEMENT (Score: {$result['replacement_score']})");
            }
        }

        $this->info('Fleet replacement evaluation completed.');
    }
}
