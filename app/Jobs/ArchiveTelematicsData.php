<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ArchiveTelematicsData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $bufferKey = 'telematics:archive:buffer';
        $batchSize = 1000;
        $records = [];

        // 1. Pop batch from Redis
        for ($i = 0; $i < $batchSize; $i++) {
            $data = Redis::rpop($bufferKey);
            if (!$data) break;

            $payload = json_decode($data, true);
            $records[] = [
                'company_id' => $payload['company_id'],
                'vehicle_id' => $payload['vehicle_id'],
                'latitude' => $payload['latitude'],
                'longitude' => $payload['longitude'],
                'speed' => $payload['speed'],
                'ignition_status' => $payload['ignition'],
                'timestamp' => \Carbon\Carbon::createFromTimestamp($payload['timestamp'])->toDateTimeString(),
            ];
        }

        // 2. Bulk Insert (High Performance)
        if (!empty($records)) {
            DB::connection('tenant')->table('vehicle_location_logs')->insert($records);
        }
    }
}
