<?php

namespace App\Http\Controllers\Web\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderHistoryController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::where('buyer_id', '=', Auth::id())
            ->where('status', 'delivered') // 'delivered' represents completed history
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('pages.user.orders.index', compact('orders'));
    }

    public function pending()
    {
        $orders = Order::where('buyer_id', '=', Auth::id())
            ->whereIn('status', ['pending', 'paid', 'shipped']) // Show active orders not yet delivered
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

        $order->load(['items.product', 'items.product.seller.sellerProfile']);

        // Identify seller for this order (assuming all items in an order belong to same seller per our design)
        $sellerProfile = $order->items->first()->product->seller->sellerProfile ?? null;

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

        // Logic for COD: delivery validation also confirms payment receipt
        // Logic for Paid: just confirms delivery
        
        $updateData = [
            'delivery_status' => 'delivered'
        ];

        if ($order->payment_method === 'cash_on_delivery' && $order->status === 'pending') {
            $updateData['status'] = 'paid'; // COD paid upon delivery
        } elseif ($order->status !== 'delivered') {
             // If not COD, status might already be paid, so we ensure it reflects delivery if needed, 
             // but 'delivered' status usually implies completion in this schema context if 'completed' doesn't exist.
             // Based on schema provided: enum('pending', 'paid', 'shipped', 'delivered'...)
             $updateData['status'] = 'delivered';
        }

        if ($order->delivery_status !== 'delivered') {
            $order->update($updateData);
            return back()->with('success', 'Livraison (et paiement) confirmée avec succès !');
        }

        return back()->with('info', 'Cette commande est déjà validée.');
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
        $sellerProfile = $order->items->first()->product->seller->sellerProfile ?? null;

        $pdf = Pdf::loadView('pdf.receipt', compact('order', 'sellerProfile'));
        
        return $pdf->download('recu-commande-' . $order->id . '.pdf');
    }
}
