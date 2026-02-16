<?php

namespace Modules\Marketplace\Services;

use Modules\Marketplace\Interfaces\CartRepositoryInterface;
use Modules\Marketplace\Interfaces\ProductRepositoryInterface;
use Modules\Marketplace\DTOs\AddToCartDTO;
use Modules\Marketplace\Entities\Cart;
use Modules\Marketplace\ValueObjects\Price;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly ProductRepositoryInterface $productRepository,
    ) {}
    
    public function addToCart(AddToCartDTO $dto): Cart
    {
        $cart = $this->cartRepository->getOrCreateForUser($dto->userId);
        $product = $this->productRepository->findById($dto->productId);
        
        if (!$product) {
            throw new \DomainException('Produit introuvable');
        }
        
        $cart->addItem($product, $dto->quantity);
        
        return $cart->fresh(['items.product.images']);
    }
    
    public function removeFromCart(string $userId, string $itemId): Cart
    {
        $cart = $this->cartRepository->getOrCreateForUser($userId);
        $cart->removeItem($itemId);
        
        return $cart->fresh(['items.product.images']);
    }
    
    public function updateCartItem(string $userId, string $itemId, int $quantity): Cart
    {
        $cart = $this->cartRepository->getOrCreateForUser($userId);
        $cart->updateQuantity($itemId, $quantity);
        
        return $cart->fresh(['items.product.images']);
    }
    
    public function clearCart(string $userId): void
    {
        $cart = $this->cartRepository->findByUserId($userId);
        
        if ($cart) {
            $cart->clear();
        }
    }
    
    public function getCart(string $userId): Cart
    {
        return $this->cartRepository->getOrCreateForUser($userId);
    }
    
    public function getCartTotal(string $userId): Price
    {
        $cart = $this->cartRepository->findByUserId($userId);
        
        if (!$cart) {
            return Price::zero();
        }
        
        return $cart->getTotalAmount();
    }

    /**
     * Obtenir les détails du panier groupés par vendeur
     */
    public function getCartDetails(string $userId): array
    {
        $cart = $this->getCart($userId);
        
        if (!$cart || $cart->items->isEmpty()) {
            return [];
        }
        
        $details = [];
        
        // Eager load relationships
        $cart->load(['items.product.seller.sellerProfile', 'items.product.images']);
        
        foreach ($cart->items as $item) {
            $product = $item->product;
            
            if (!$product) continue;
            
            $sellerId = $product->seller_id;
            
            if (!isset($details[$sellerId])) {
                $shopName = 'Boutique';
                if ($product->seller && $product->seller->sellerProfile) {
                    $shopName = $product->seller->sellerProfile->shop_name;
                }
                
                $details[$sellerId] = [
                    'shop_name' => $shopName,
                    'items' => [],
                    'subtotal' => 0.0,
                ];
            }
            
            $itemTotal = $item->quantity * $item->price_at_addition;
            
            $details[$sellerId]['items'][] = [
                'id' => $item->id,
                'image' => $product->thumbnail_url,
                'title' => $product->title,
                'price' => $item->price_at_addition,
                'quantity' => $item->quantity,
                'max_stock' => $product->stock_quantity,
                'subtotal' => $itemTotal,
            ];
            
            $details[$sellerId]['subtotal'] += $itemTotal;
        }
        
        return $details;
    }
}
