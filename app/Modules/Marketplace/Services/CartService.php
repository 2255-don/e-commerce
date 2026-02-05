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
}
