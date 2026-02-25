<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * List User Orders
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Show Order Details
     */
    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.product', 'buyer'])
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * Confirm Delivery
     */
    public function confirm(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        try {
            $this->orderService->confirmOrder($order);
            return response()->json(['message' => 'Commande confirmée avec succès.', 'status' => $order->refresh()->status]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Report Issue (Dispute)
     */
    public function reportIssue(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        try {
            // Re-use logic from OrderService::refundOrder (which now handles disputes)
            $this->orderService->refundOrder($order); 
            return response()->json(['message' => 'Litige signalé avec succès.', 'status' => 'dispute']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
