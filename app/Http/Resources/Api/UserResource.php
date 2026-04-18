<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'notification_preferences' => $this->notification_preferences ?? [
                'email' => true,
                'sms' => false,
                'whatsapp' => false,
            ],
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
