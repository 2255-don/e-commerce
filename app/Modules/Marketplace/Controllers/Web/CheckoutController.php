<?php

namespace Modules\Marketplace\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Services\OrderService;
use Modules\Marketplace\Services\CartService;
use Modules\Marketplace\Repositories\OrderRepository;
use Modules\Marketplace\DTOs\PlaceOrderDTO;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CartService $cartService,
        private readonly OrderRepository $orderRepository,
    ) {}
    
    public function index()
    {
        $cart = $this->cartService->getCart(auth()->id());
        
        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        return view('pages.checkout.index', compact('cart'));
    }
    
    public function process(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:wallet,mobile_money',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            $dto = PlaceOrderDTO::fromRequest($validated, auth()->id());
            $order = $this->orderService->placeOrder($dto);
            
            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Order placed successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function success(string $orderId)
    {
        $order = $this->orderRepository->findById($orderId);
        
        if (!$order || $order->buyer_id !== auth()->id()) {
            abort(404);
        }
        
        return view('pages.checkout.success', compact('order'));
    }
}
