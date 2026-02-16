<?php

namespace Modules\Marketplace\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Repositories\OrderRepository;
use Modules\Marketplace\Entities\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
    ) {}
    
    public function index(Request $request)
    {
        $orders = $this->orderRepository->getUserOrders($request->user()->id);
        
        return view('marketplace::orders.index', compact('orders'));
    }
    
    public function pending(Request $request)
    {
        // Note: OrderRepository needs update to support status filtering or we use direct query here temporarily
        // Ideally we should add getPendingUserOrders to repository
        $orders = Order::where('buyer_id', $request->user()->id)
            ->whereIn('status', ['pending', 'paid', 'shipped'])
            ->with(['items.product'])
            ->latest()
            ->paginate(10);
            
        return view('marketplace::orders.index', compact('orders'));
    }
    
    public function show(Request $request, string $id)
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order || $order->buyer_id !== $request->user()->id) {
            abort(403);
        }
        
        // Identify seller for this order
        $sellerProfile = $order->items->first()->product->seller->sellerProfile ?? null;
        
        return view('marketplace::orders.show', compact('order', 'sellerProfile'));
    }
    
    public function confirmDelivery(Request $request, string $id)
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order || $order->buyer_id !== $request->user()->id) {
            abort(403);
        }
        
        $updateData = [
            'delivery_status' => 'delivered'
        ];

        if ($order->payment_method === 'cash_on_delivery' && $order->status === 'pending') {
            $updateData['status'] = 'paid';
        } elseif ($order->status !== 'delivered') {
             $updateData['status'] = 'delivered';
        }

        if ($order->delivery_status !== 'delivered') {
            $order->update($updateData);
            return back()->with('success', 'Livraison (et paiement) confirmée avec succès !');
        }

        return back()->with('info', 'Cette commande est déjà validée.');
    }
    
    public function downloadReceipt(Request $request, string $id)
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order || $order->buyer_id !== $request->user()->id) {
            abort(403);
        }
        
        $sellerProfile = $order->items->first()->product->seller->sellerProfile ?? null;

        $pdf = Pdf::loadView('pdf.receipt', compact('order', 'sellerProfile'));
        
        return $pdf->download('recu-commande-' . $order->id . '.pdf');
    }
}
