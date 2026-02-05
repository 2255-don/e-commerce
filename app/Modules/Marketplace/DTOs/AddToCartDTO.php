<?php

namespace Modules\Marketplace\DTOs;

/**
 * Add To Cart DTO
 */
final class AddToCartDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly string $productId,
        public readonly int $quantity = 1,
    ) {}
    
    public static function fromRequest(array $data, string $userId): self
    {
        return new self(
            userId: $userId,
            productId: $data['product_id'],
            quantity: (int) ($data['quantity'] ?? 1),
        );
    }
    
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ];
    }
}
