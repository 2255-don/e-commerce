<?php

namespace Modules\Marketplace\DTOs;

/**
 * Product Filter DTO
 */
final class ProductFilterDTO
{
    public function __construct(
        public readonly ?string $categoryId = null,
        public readonly ?string $search = null,
        public readonly ?float $minPrice = null,
        public readonly ?float $maxPrice = null,
        public readonly ?string $sellerId = null,
        public readonly ?string $type = null,
        public readonly bool $activeOnly = true,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDirection = 'desc',
    ) {}
    
    public static function fromRequest(array $data): self
    {
        return new self(
            categoryId: $data['category_id'] ?? null,
            search: $data['search'] ?? null,
            minPrice: isset($data['min_price']) ? (float) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            sellerId: $data['seller_id'] ?? null,
            type: $data['type'] ?? null,
            activeOnly: isset($data['active_only']) ? (bool) $data['active_only'] : true,
            sortBy: $data['sort_by'] ?? 'created_at',
            sortDirection: $data['sort_direction'] ?? 'desc',
        );
    }
}
