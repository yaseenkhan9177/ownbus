<?php

namespace App\Events\Intelligence;

use App\Models\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FleetStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vehicleId;
    public $status;

    public function __construct($vehicleId, $status)
    {
        $this->vehicleId = $vehicleId;
        $this->status = $status;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('fleet.status')
        ];
    }
}
