<?php

namespace Modules\Seller\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Repositories\OrderRepository;

class SellerOrderController extends Controller
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
    ) {}
    
    public function index()
    {
        $orders = $this->orderRepository->getSellerOrders(auth()->id(), 15);
        return view('pages.seller.orders.index', compact('orders'));
    }
    
    public function show(string $id)
    {
        $order = $this->orderRepository->findById($id);
        
        // Check if order contains items from this seller
        $hasSellerItems = $order->items->where('seller_id', auth()->id())->count() > 0;
        
        if (!$order || !$hasSellerItems) {
            abort(404);
        }
        
        return view('pages.seller.orders.show', compact('order'));
    }
}
