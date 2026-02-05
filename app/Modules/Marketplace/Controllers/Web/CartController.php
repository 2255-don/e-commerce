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
        $cart = $this->cartService->getCart(auth()->id());
        return view('pages.cart.index', compact('cart'));
    }
    
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|string|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $dto = AddToCartDTO::fromRequest($validated, $request->user()->id);
        $this->cartService->addItem($request->user()->id, $dto);
        
        return redirect()->back()->with('success', 'Product added to cart');
    }
    
    public function update(Request $request, string $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $this->cartService->updateItem(auth()->id(), $itemId, $validated['quantity']);
        
        return redirect()->back()->with('success', 'Cart updated');
    }
    
    public function remove(string $itemId)
    {
        $this->cartService->removeItem(auth()->id(), $itemId);
        
        return redirect()->back()->with('success', 'Item removed from cart');
    }
    
    public function clear()
    {
        $this->cartService->clearCart(auth()->id());
        
        return redirect()->back()->with('success', 'Cart cleared');
    }
}
