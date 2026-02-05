<?php

namespace Modules\Marketplace\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Services\CartService;
use Modules\Marketplace\DTOs\AddToCartDTO;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}
    
    /**
     * Get current user's cart
     */
    public function index(Request $request)
    {
        $cart = $this->cartService->getCart($request->user()->id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cart->items,
                'total' => $cart->total,
                'item_count' => $cart->items->count(),
            ],
        ]);
    }
    
    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|string|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        try {
            $dto = AddToCartDTO::fromRequest($validated, $request->user()->id);
            $cart = $this->cartService->addItem($request->user()->id, $dto);
            
            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Update cart item quantity
     */
    public function update(Request $request, string $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        try {
            $cart = $this->cartService->updateItem(
                $request->user()->id,
                $itemId,
                $validated['quantity']
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Remove item from cart
     */
    public function remove(Request $request, string $itemId)
    {
        try {
            $cart = $this->cartService->removeItem($request->user()->id, $itemId);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
