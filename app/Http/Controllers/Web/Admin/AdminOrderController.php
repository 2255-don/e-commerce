<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected $orderService;

    public function __construct(\App\Services\Order\OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of all orders.
     */
    public function index()
    {
        $orders = Order::with(['buyer', 'items.product.seller.user'])
            ->latest()
            ->paginate(20);

        return view('pages.admin.orders.index', compact('orders'));
    }

    public function refund(Order $order)
    {
        try {
            $this->orderService->adminRefund($order);
            return back()->with('success', 'Commande remboursée avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
