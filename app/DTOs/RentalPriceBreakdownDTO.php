<?php

namespace App\DTOs;

class RentalPriceBreakdownDTO
{
    /**
     * @param float $base_amount
     * @param PricingAdjustmentDTO[] $adjustments
     * @param float $subtotal
     * @param float $tax
     * @param float $final_amount
     * @param array $metadata
     */
    public function __construct(
        public float $base_amount,
        public array $adjustments,
        public float $subtotal,
        public float $tax,
        public float $final_amount,
        public array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'base_amount' => $this->base_amount,
            'adjustments' => array_map(fn($adj) => $adj->toArray(), $this->adjustments),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'final_amount' => $this->final_amount,
            'metadata' => $this->metadata,
        ];
    }
}
