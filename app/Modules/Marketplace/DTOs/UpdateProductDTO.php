<?php

namespace Modules\Marketplace\DTOs;

/**
 * Update Product DTO
 */
final class UpdateProductDTO
{
    public function __construct(
        public readonly ?string $categoryId = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?float $price = null,
        public readonly ?int $stockQuantity = null,
        public readonly ?string $type = null,
        public readonly ?bool $isActive = null,
    ) {}
    
    public static function fromRequest(array $data): self
    {
        return new self(
            categoryId: $data['category_id'] ?? null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            stockQuantity: isset($data['stock_quantity']) ? (int) $data['stock_quantity'] : null,
            type: $data['type'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
        );
    }
    
    public function toArray(): array
    {
        $data = [];
        
        if ($this->categoryId !== null) $data['category_id'] = $this->categoryId;
        if ($this->title !== null) $data['title'] = $this->title;
        if ($this->description !== null) $data['description'] = $this->description;
        if ($this->price !== null) $data['price'] = $this->price;
        if ($this->stockQuantity !== null) $data['stock_quantity'] = $this->stockQuantity;
        if ($this->type !== null) $data['type'] = $this->type;
        if ($this->isActive !== null) $data['is_active'] = $this->isActive;
        
        return $data;
    }
}
