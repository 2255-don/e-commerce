<?php

namespace Modules\Marketplace\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Services\CartService;
use Modules\Marketplace\DTOs\AddToCartDTO;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}
    
    public function index()
    {
        $cart = $this->cartService->getCartDetails(auth()->id());
        $total = $this->cartService->getCartTotal(auth()->id())->getAmount();
        
        return view('marketplace::cart', compact('cart', 'total'));
    }

    public function getSidebar()
    {
        $cart = $this->cartService->getCartDetails(auth()->id());
        $total = $this->cartService->getCartTotal(auth()->id())->getAmount();
        
        $html = view('marketplace::cart.sidebar', compact('cart', 'total'))->render();
        
        return response()->json(['html' => $html]);
    }
    
    public function addAjax(string $productId)
    {
        try {
            $dto = AddToCartDTO::fromRequest([
                'product_id' => $productId,
                'quantity' => 1,
            ], auth()->id());
            
            $cart = $this->cartService->addToCart($dto);
            $cartCount = auth()->user()->cartItemsCount();
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cartCount' => $cartCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|string|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $dto = AddToCartDTO::fromRequest($validated, $request->user()->id);
        $cart = $this->cartService->addToCart($dto);
        
        return redirect()->back()->with('success', 'Product added to cart');
    }
    
    public function update(Request $request, string $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cart = $this->cartService->updateCartItem(
            $request->user()->id,
            $itemId,
            $validated['quantity']
        );
        
        return redirect()->back()->with('success', 'Cart updated');
    }
    
    public function remove(Request $request, string $itemId)
    {
        $cart = $this->cartService->removeFromCart($request->user()->id, $itemId);
        
        return redirect()->back()->with('success', 'Item removed from cart');
    }
    
    public function clear()
    {
        $this->cartService->clearCart(auth()->id());
        
        return redirect()->back()->with('success', 'Cart cleared');
    }
}
