<?php

namespace App\Events\Intelligence;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RevenueUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $revenueData;

    public function __construct($revenueData)
    {
        $this->revenueData = $revenueData;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('intelligence.revenue')
        ];
    }
}
