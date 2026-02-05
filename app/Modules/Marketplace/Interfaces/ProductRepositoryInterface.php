<?php

namespace Modules\Marketplace\Interfaces;

use Modules\Marketplace\Entities\Product;
use Modules\Marketplace\DTOs\ProductFilterDTO;

interface ProductRepositoryInterface
{
    public function findById(string $id): ?Product;
    public function findBySlug(string $slug): ?Product;
    public function getBySeller(string $sellerId, int $perPage = 15);
    public function getByCategory(string $categoryId, int $perPage = 15);
    public function search(ProductFilterDTO $filters, int $perPage = 15);
    public function getFeatured(int $limit = 10);
    public function create(array $data): Product;
    public function update(string $id, array $data): Product;
    public function delete(string $id): bool;
}
