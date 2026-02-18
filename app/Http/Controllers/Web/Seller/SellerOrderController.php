<?php

namespace App\Http\Controllers\Web\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the seller's orders.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->sellerProfile) {
            return redirect()->route('dashboard');
        }

        $sellerId = $user->sellerProfile->id;

        // Get orders where items belong to this seller
        // Since we split orders by seller at creation, an order effectively belongs to one seller.
        $orders = Order::whereHas('items.product', function ($query) use ($sellerId) {
                $query->where('seller_id', $sellerId);
            })
            ->with(['buyer', 'items.product'])
            ->latest()
            ->paginate(20);

        return view('pages.seller.orders.index', compact('orders'));
    }

    /**
     * Mark an order as shipped.
     */
    public function markAsShipped(Order $order)
    {
        try {
            // Verify ownership
            $user = Auth::user();
            $sellerId = $user->sellerProfile->id;
            
            // Check if any item in this order belongs to the seller
            // (Again, strictly one seller per order, but good to be safe)
            $belongsToSeller = $order->items()->whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })->exists();

            if (!$belongsToSeller) {
                abort(403);
            }

            $this->orderService->markAsShipped($order);

            return back()->with('success', 'Commande marquée comme expédiée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
