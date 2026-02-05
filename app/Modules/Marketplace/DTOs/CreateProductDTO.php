<?php

namespace Modules\Marketplace\DTOs;

use Modules\Marketplace\ValueObjects\ProductType;

/**
 * Create Product DTO
 */
final class CreateProductDTO
{
    public function __construct(
        public readonly string $sellerId,
        public readonly string $categoryId,
        public readonly string $title,
        public readonly string $description,
        public readonly float $price,
        public readonly int $stockQuantity,
        public readonly string $type = 'physical',
    ) {}
    
    public static function fromRequest(array $data, string $sellerId): self
    {
        return new self(
            sellerId: $sellerId,
            categoryId: $data['category_id'],
            title: $data['title'],
            description: $data['description'] ?? '',
            price: (float) $data['price'],
            stockQuantity: (int) ($data['stock_quantity'] ?? 0),
            type: $data['type'] ?? 'physical',
        );
    }
    
    public function toArray(): array
    {
        return [
            'seller_id' => $this->sellerId,
            'category_id' => $this->categoryId,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stockQuantity,
            'type' => $this->type,
            'is_active' => true,
        ];
    }
}
