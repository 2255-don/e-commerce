<?php

namespace Modules\Marketplace\Services;

use Modules\Marketplace\Interfaces\ProductRepositoryInterface;

class StockService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}
    
    public function checkAvailability(string $productId, int $quantity): bool
    {
        $product = $this->productRepository->findById($productId);
        
        if (!$product) {
            return false;
        }
        
        return $product->isInStock($quantity);
    }
    
    public function reserveStock(string $productId, int $quantity): void
    {
        $product = $this->productRepository->findById($productId);
        
        if (!$product) {
            throw new \DomainException('Produit introuvable');
        }
        
        $product->decreaseStock($quantity);
    }
    
    public function releaseStock(string $productId, int $quantity): void
    {
        $product = $this->productRepository->findById($productId);
        
        if (!$product) {
            throw new \DomainException('Produit introuvable');
        }
        
        $product->increaseStock($quantity);
    }
    
    public function updateStock(string $productId, int $quantity): void
    {
        $product = $this->productRepository->findById($productId);
        
        if (!$product) {
            throw new \DomainException('Produit introuvable');
        }
        
        $product->stock_quantity = $quantity;
        $product->save();
    }
}
