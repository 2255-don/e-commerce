<?php

namespace Modules\Marketplace\Repositories;

use Modules\Marketplace\Entities\Product;
use Modules\Marketplace\Interfaces\ProductRepositoryInterface;
use Modules\Marketplace\DTOs\ProductFilterDTO;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(string $id): ?Product
    {
        return Product::with(['seller', 'category', 'images'])->find($id);
    }
    
    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['seller', 'category', 'images'])
            ->where('slug', $slug)
            ->first();
    }
    
    public function getBySeller(string $sellerId, int $perPage = 15)
    {
        return Product::where('seller_id', $sellerId)
            ->with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getByCategory(string $categoryId, int $perPage = 15)
    {
        return Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(['seller', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function search(ProductFilterDTO $filters, int $perPage = 15)
    {
        $query = Product::query()->with(['seller', 'category', 'images']);
        
        if ($filters->activeOnly) {
            $query->where('is_active', true);
        }
        
        if ($filters->categoryId) {
            $query->where('category_id', $filters->categoryId);
        }
        
        if ($filters->search) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters->search}%")
                  ->orWhere('description', 'like', "%{$filters->search}%");
            });
        }
        
        if ($filters->minPrice !== null) {
            $query->where('price', '>=', $filters->minPrice);
        }
        
        if ($filters->maxPrice !== null) {
            $query->where('price', '<=', $filters->maxPrice);
        }
        
        if ($filters->sellerId) {
            $query->where('seller_id', $filters->sellerId);
        }
        
        if ($filters->type) {
            $query->where('type', $filters->type);
        }
        
        $query->orderBy($filters->sortBy, $filters->sortDirection);
        
        return $query->paginate($perPage);
    }
    
    public function getFeatured(int $limit = 10)
    {
        return Product::where('is_active', true)
            ->with(['seller', 'category', 'images'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
    
    public function create(array $data): Product
    {
        return Product::create($data);
    }
    
    public function update(string $id, array $data): Product
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product->fresh();
    }
    
    public function delete(string $id): bool
    {
        return Product::destroy($id) > 0;
    }
}
