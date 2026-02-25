<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class AdminOrderApiController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Admin Refund
     */
    public function refund($id)
    {
        $order = Order::findOrFail($id);

        try {
            $this->orderService->adminRefund($order);
            return response()->json(['message' => 'Commande remboursée avec succès.', 'status' => 'cancelled']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
