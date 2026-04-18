<?php

namespace App\Console\Commands\Intelligence;

use Illuminate\Console\Command;

class CalculateDriverRisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drivers:calculate-risk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate daily safety risk scores for all active drivers';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Intelligence\DriverRiskScoreService $riskService)
    {
        $drivers = \App\Models\Driver::where('status', 'active')->get();
        $this->info("Calculating risk for {$drivers->count()} drivers...");

        foreach ($drivers as $driver) {
            $result = $riskService->calculate($driver);

            \Illuminate\Support\Facades\DB::connection('tenant')->table('driver_risk_snapshots')->insert([
                'driver_id' => $driver->id,
                'score' => $result['score'],
                'risk_level' => $result['level'],
                'breakdown_json' => json_encode($result['breakdown']),
                'calculated_at' => now(),
            ]);

            // Operational Alert: If High Risk, notify
            if ($result['level'] === 'high') {
                $this->warn("Driver #{$driver->id} is HIGH RISK (Score: {$result['score']})");

                // Notify Company Admins
                $admins = \App\Models\User::where('company_id', $driver->company_id)
                    ->whereHas('role', function ($q) {
                        $q->where('name', 'admin');
                    })->get();

                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\Intelligence\HighRiskDriverAlert($driver, $result['score']));
            }
        }

        $this->info('Risk calculation completed.');
    }
}
