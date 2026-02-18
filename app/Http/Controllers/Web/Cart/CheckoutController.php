<?php

namespace App\Http\Controllers\Web\Cart;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getGroupedBySeller();
        $total = $this->cartService->total();

        return view('pages.marketplace.cart', compact('cart', 'total'));
    }

    public function add(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $count = $this->cartService->add($product, $request->input('quantity', 1));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté au panier',
                'cartCount' => $count
            ]);
        }

        return redirect()->back()->with('success', 'Produit ajouté au panier');
    }

    public function update(Request $request, $productId)
    {
        $this->cartService->update($productId, $request->quantity);
        return back();
    }

    public function remove(Request $request, $productId)
    {
        $this->cartService->remove($productId);
        
        if ($request->ajax() || $request->wantsJson()) {
             return response()->json([
                'success' => true,
                'message' => 'Produit retiré',
                'cartTotal' => $this->cartService->total()
            ]);
        }

        return redirect()->back()->with('success', 'Produit retiré du panier');
    }

    /**
     * Get cart details for Modal (AJAX)
     */
    public function cartDetails()
    {
        $cart = $this->cartService->getGroupedBySeller();
        $total = $this->cartService->total();
        
        // Return view fragment or JSON. JSON is cleaner for Vue/JS handling, 
        // but View Fragment is easier if we just want to replace HTML.
        // Let's return HTML fragment for the modal body to keep it simple with Blade.
        
        $html = view('pages.marketplace.cart_modal_content', compact('cart', 'total'))->render();

        return response()->json([
            'html' => $html,
            'count' => array_sum(array_map(function($sellerGroup) {
                return count($sellerGroup['items']);
            }, $cart)) 
        ]);
    }

    public function process(Request $request, OrderService $orderService)
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            $cart = $this->cartService->getCart();
            $total = $this->cartService->total();

            if (empty($cart)) {

                return redirect()->route('marketplace.index');
            }

            // dd($user, $cart, $total);

            // Execute Checkout
            $orderService->processCheckout($user, $cart, $total, $request->input('type', 'wallet'));

            // Clear Cart
            $this->cartService->clear();

            return redirect()->route('user.orders.pending')->with('success', 'Commande validée avec succès !');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]); // e.g., Insufficient funds
        }
    }
}
