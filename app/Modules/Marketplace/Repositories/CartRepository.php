<?php

namespace Modules\Marketplace\Repositories;

use Modules\Marketplace\Entities\Cart;
use Modules\Marketplace\Interfaces\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function findByUserId(string $userId): ?Cart
    {
        return Cart::where('user_id', $userId)
            ->with(['items.product.images'])
            ->first();
    }
    
    public function getOrCreateForUser(string $userId): Cart
    {
        $cart = $this->findByUserId($userId);
        
        if (!$cart) {
            $cart = Cart::create(['user_id' => $userId]);
        }
        
        return $cart;
    }
    
    public function clear(string $cartId): void
    {
        $cart = Cart::find($cartId);
        if ($cart) {
            $cart->clear();
        }
    }
}
