<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use App\Models\Product;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get Cart Content
     */
    public function index()
    {
        // Use getGroupedBySeller if the frontend wants it grouped, 
        // or getCart for a flat list. Let's return both for flexibility.
        
        return response()->json([
            'items' => $this->cartService->getCart(),
            'grouped' => $this->cartService->getGroupedBySeller(),
            'total' => $this->cartService->total()
        ]);
    }

    /**
     * Add Item
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $this->cartService->add($product, $request->input('quantity', 1));

        return response()->json(['message' => 'Produit ajouté au panier.', 'cart' => $this->index()->original]);
    }

    /**
     * Update Quantity
     */
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        $this->cartService->update($productId, $request->quantity);

        return response()->json(['message' => 'Panier mis à jour.', 'cart' => $this->index()->original]);
    }

    /**
     * Remove Item
     */
    public function remove($productId)
    {
        $this->cartService->remove($productId);

        return response()->json(['message' => 'Produit retiré.', 'cart' => $this->index()->original]);
    }

    /**
     * Clear Cart
     */
    public function clear()
    {
        $this->cartService->clear();
        return response()->json(['message' => 'Panier vidé.', 'cart' => $this->index()->original]);
    }
}
