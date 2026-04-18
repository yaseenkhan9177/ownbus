<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class MaintenanceAlert extends Notification
{
    use Queueable;

    private $vehicle;
    private $serviceType;
    private $reason;

    public function __construct($vehicle, $serviceType, $reason)
    {
        $this->vehicle = $vehicle;
        $this->serviceType = $serviceType;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'vehicle_id' => $this->vehicle->id,
            'vehicle_name' => $this->vehicle->name ?? 'Vehicle',
            'service_type' => $this->serviceType,
            'reason' => $this->reason,
            'message' => "Preventive maintenance '{$this->serviceType}' is {$this->reason} for vehicle {$this->vehicle->name}.",
        ];
    }
}
