<?php

namespace Modules\Marketplace\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Services\OrderService;
use Modules\Marketplace\DTOs\PlaceOrderDTO;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}
    
    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->getUserOrders($request->user()->id);
        
        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }
    
    /**
     * Create new order
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:wallet,mobile_money',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            $dto = PlaceOrderDTO::fromRequest($validated);
            $order = $this->orderService->placeOrder($request->user()->id, $dto);
            
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Get order details
     */
    public function show(Request $request, string $id)
    {
        $order = $this->orderService->getOrderById($id);
        
        if (!$order || $order->buyer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }
    
    /**
     * Cancel order
     */
    public function cancel(Request $request, string $id)
    {
        try {
            $order = $this->orderService->cancelOrder($id, $request->user()->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
