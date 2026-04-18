<?php

namespace App\DTOs;

class PricingAdjustmentDTO
{
    public function __construct(
        public string $name,
        public string $type, // 'seasonal', 'weekend', 'vip', 'duration', 'surge', 'coupon'
        public string $adjustment_type, // 'percentage', 'fixed'
        public float $adjustment_value,
        public float $calculated_amount
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'adjustment_type' => $this->adjustment_type,
            'adjustment_value' => $this->adjustment_value,
            'calculated_amount' => $this->calculated_amount,
        ];
    }
}
