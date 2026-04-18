<?php

namespace App\Events\Intelligence;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertTriggered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alert;

    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('intelligence.alerts')
        ];
    }
}
