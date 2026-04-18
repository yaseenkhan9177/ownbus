<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'type' => $this->type,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'seating_capacity' => $this->seating_capacity,
            'daily_rate' => (float) $this->daily_rate,
            'status' => $this->status,
            'vehicle_number' => $this->vehicle_number,
            'current_odometer' => $this->current_odometer,
            'fuel_type' => $this->fuel_type,
            'amenities' => $this->amenities ?? [],
            'images' => $this->images ?? [],
            'description' => $this->description,
        ];
    }
}
