<?php

namespace Modules\Marketplace\DTOs;

/**
 * Place Order DTO
 */
final class PlaceOrderDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly string $paymentMethod,
        public readonly ?string $notes = null,
        public readonly ?string $deliveryAddress = null,
    ) {}
    
    public static function fromRequest(array $data, string $userId): self
    {
        return new self(
            userId: $userId,
            paymentMethod: $data['payment_method'],
            notes: $data['notes'] ?? null,
            deliveryAddress: $data['delivery_address'] ?? null,
        );
    }
    
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'payment_method' => $this->paymentMethod,
            'notes' => $this->notes,
            'delivery_address' => $this->deliveryAddress,
        ];
    }
}
