<?php

namespace App\Http\Controllers\Web\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderHistoryController extends Controller
{
    protected $orderService;

    public function __construct(\App\Services\Order\OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::where('buyer_id', '=', Auth::id())
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('pages.user.orders.index', compact('orders'));
    }

    public function pending()
    {
        $orders = Order::where('buyer_id', '=', Auth::id())
            ->whereIn('status', ['pending', 'paid', 'pending_payment', 'shipped'])
            ->with(['items.product'])
            ->latest()
            ->paginate(10);
        
        return view('pages.user.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Security check
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product.sellerUser']);

        // Identify seller for this order
        $firstItem = $order->items->first();
        $sellerProfile = $firstItem ? $firstItem->product->seller : null;

        return view('pages.user.orders.show', compact('order', 'sellerProfile'));
    }

    /**
     * Confirm delivery of the order.
     */
    public function confirmDelivery(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->orderService->confirmOrder($order);
            return back()->with('success', 'Réception confirmée ! Les fonds ont été libérés au vendeur.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Report an issue (Request Refund).
     */
    public function reportIssue(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->orderService->refundOrder($order);
            return back()->with('success', 'Votre signalement a été enregistré. Un administrateur va examiner votre demande.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Download PDF receipt.
     */
    public function downloadReceipt(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'buyer']);
        $sellerProfile = $order->items->first()->product->seller ?? null;

        $pdf = Pdf::loadView('pdf.receipt', compact('order', 'sellerProfile'));
        
        return $pdf->download('recu-commande-' . $order->delivery_code . '.pdf');
    }
}
