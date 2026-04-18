<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminBroadcastSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $broadcast;

    /**
     * Create a new event instance.
     */
    public function __construct(\App\Models\AdminBroadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->broadcast->company_id) {
            return [new PrivateChannel('company.' . $this->broadcast->company_id . '.broadcasts')];
        }

        return [new PrivateChannel('broadcasts.' . $this->broadcast->target_role)];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->broadcast->id,
            'message' => $this->broadcast->message,
            'target_role' => $this->broadcast->target_role,
            'created_at' => $this->broadcast->created_at->toIso8601String(),
        ];
    }
}
