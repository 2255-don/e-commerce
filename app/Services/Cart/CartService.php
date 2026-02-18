<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Get or create the cart for current user/session.
     */
    protected function getCartInstance()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            // Guest support (optional based on requirements, but good practice)
            $sessionId = Session::getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }
        return $cart;
    }

    /**
     * Get cart content (formatted for view).
     */
    public function getCart()
    {
        $cart = $this->getCartInstance();
        
        // Return array format compatible with existing views
        // Or better: Return Collection of items and adapt view.
        // Let's adapt to existing structure: id => [details]
        
        $items = $cart->items()->with('product.seller')->get();
        $formatted = [];

        foreach ($items as $item) {
            $formatted[$item->product->id] = [
                'id' => $item->product->id,
                'title' => $item->product->title,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
                'seller_id' => $item->product->seller_id,
                'shop_name' => $item->product->seller->shop_name ?? 'Vendeur',
                'image' => $item->product->thumbnail_url,
                'max_stock' => $item->product->stock_quantity,
                'cart_item_id' => $item->id
            ];
        }
        
        return $formatted;
    }

    /**
     * Add product to cart.
     */
    public function add(Product $product, int $quantity = 1)
    {
        $cart = $this->getCartInstance();
        
        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price_at_addition' => $product->price
            ]);
        }

        return $cart->items()->count();
    }

    /**
     * Update quantity.
     */
    public function update(string $productId, int $quantity)
    {
        $cart = $this->getCartInstance();
        
        if ($quantity > 0) {
            $cart->items()->where('product_id', $productId)->update(['quantity' => $quantity]);
        } else {
            $this->remove($productId);
        }
    }

    /**
     * Remove item.
     */
    public function remove(string $productId)
    {
        $cart = $this->getCartInstance();
        $cart->items()->where('product_id', $productId)->delete();
    }

    /**
     * Clear cart.
     */
    public function clear()
    {
        $cart = $this->getCartInstance();
        $cart->items()->delete();
    }

    /**
     * Calculate total price.
     */
    public function total()
    {
        $cart = $this->getCartInstance();
        return $cart->items->sum(function($item) {
             return $item->quantity * $item->product->price;
        });
    }

    /**
     * Group items by seller for checkout display.
     */
    public function getGroupedBySeller()
    {
        $cartItems = $this->getCart(); // Reusing the formatted array logic
        $grouped = [];

        foreach ($cartItems as $item) {
            $sellerId = $item['seller_id'];
            if (!isset($grouped[$sellerId])) {
                $grouped[$sellerId] = [
                    'shop_name' => $item['shop_name'],
                    'items' => [],
                    'subtotal' => 0
                ];
            }
            $grouped[$sellerId]['items'][] = $item;
            $grouped[$sellerId]['subtotal'] += $item['price'] * $item['quantity'];
        }

        return $grouped;
    }
}
