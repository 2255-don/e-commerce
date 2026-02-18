<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction; // Assuming Transaction model exists
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class OrderService
{
    protected $paymentService;

    public function __construct(\App\Services\Payment\PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Process checkout from cart.
     */
    public function processCheckout($user, array $cartItems, $totalAmount, $paymentMethod = 'wallet')
    {
        try {
            return DB::transaction(function () use ($user, $cartItems, $totalAmount, $paymentMethod) {
                
                // 1. Handle Payment (Escrow)
                if ($paymentMethod === 'wallet') {
                    $wallet = $user->wallet;
                    if (!$wallet) {
                        throw new Exception("Aucun portefeuille trouvé.");
                    }

                    // Identify Platform Wallet (Super Admin)
                    $platformWallet = $this->getPlatformWallet();
                    if (!$platformWallet) {
                        throw new Exception("Erreur technique : Portefeuille plateforme introuvable.");
                    }

                    // Transfer Funds to Platform (Escrow)
                    $this->paymentService->transfer(
                        $wallet, 
                        $platformWallet, 
                        $totalAmount, 
                        "Paiement Commande (En attente de livraison)"
                    );
                }

                // 2. Group items by Seller
                $groupedItems = [];
                foreach ($cartItems as $item) {
                    $sellerId = $item['seller_id'];
                    if (!isset($groupedItems[$sellerId])) {
                        $groupedItems[$sellerId] = [];
                    }
                    $groupedItems[$sellerId][] = $item;
                }

                // 3. Create Orders (One per Seller)
                $orders = [];
                foreach ($groupedItems as $sellerId => $items) {
                    $orderTotal = 0;
                    foreach ($items as $item) {
                        $orderTotal += $item['price'] * $item['quantity'];
                    }

                    // Determine Statuses
                    if ($paymentMethod === 'cash_on_delivery') {
                        $orderStatus = 'pending_payment';
                        $deliveryStatus = 'pending';
                    } else {
                        $orderStatus = 'pending'; // Paid (Escrowed), waiting for delivery
                        $deliveryStatus = 'pending';
                    }

                    $order = Order::create([
                        'buyer_id' => $user->id,
                        'total_amount' => $orderTotal,
                        'status' => $orderStatus,
                        'payment_method' => $paymentMethod,
                        'delivery_status' => $deliveryStatus,
                        'delivery_code' => strtoupper(Str::random(6)),
                        'reference' => 'ORD-' . strtoupper(Str::random(10))
                    ]);

                    // 4. Create Order Items & Update Stock
                    foreach ($items as $itemData) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $itemData['id'], 
                            'seller_id' => $sellerId, // Added seller_id
                            'quantity' => $itemData['quantity'],
                            'price_at_purchase' => $itemData['price'], // Renamed from unit_price
                            'subtotal' => $itemData['price'] * $itemData['quantity'] // Added subtotal
                        ]);

                        // Decrement Stock
                        $product = Product::find($itemData['id']);
                        if ($product) {
                            $product->decrement('stock_quantity', $itemData['quantity']);
                        }
                    }

                    $orders[] = $order;
                }

                return $orders;
            });
        } catch (Exception $e) {
            // Log error
            \Illuminate\Support\Facades\Log::error("Erreur Checkout: " . $e->getMessage());
            throw $e; // Re-throw to be processed by controller
        }
    }

    /**
     * Mark order as shipped (Vendor Action)
     */
    public function markAsShipped(Order $order)
    {
        if ($order->delivery_status !== 'pending') {
            throw new Exception("Statut de livraison incompatible.");
        }

        $order->update(['delivery_status' => 'shipped']);
        // TODO: Notify Buyer
    }

    /**
     * Confirm Order Receipt & Release Funds (Buyer Action)
     */
    public function confirmOrder(Order $order)
    {
        if ($order->status !== 'pending' && $order->delivery_status !== 'shipped') {
             // Allow confirmation even if status is pending (e.g. instant delivery) but ideally shipped
        }
        
        if ($order->status === 'completed') {
             throw new Exception("Commande déjà terminée.");
        }

        return DB::transaction(function () use ($order) {
            
            // Release Funds if paid by Wallet
            if ($order->payment_method === 'wallet') {
                $platformWallet = $this->getPlatformWallet();
                
                // Find Seller Wallet
                // Order items -> Product -> Seller
                // Assuming all items in order belong to same seller (logic in checkout ensures this)
                $firstItem = $order->items()->first();
                $product = $firstItem->product;
                $sellerProfile = $product->seller;
                $sellerUser = $sellerProfile->user;
                $sellerWallet = $sellerUser->wallet;

                if (!$sellerWallet) {
                     throw new Exception("Portefeuille vendeur introuvable.");
                }

                // Calculate Commission
                $commissionRate = $sellerProfile->commission_rate ?? 0;
                $commissionAmount = $order->total_amount * ($commissionRate / 100);
                $netAmount = $order->total_amount - $commissionAmount;

                // Transfer Net Amount to Seller
                $this->paymentService->transfer(
                    $platformWallet,
                    $sellerWallet,
                    $netAmount,
                    "Vente Commande #{$order->delivery_code} (Reçu)"
                );

                // Commission stays in Platform Wallet (already there)
                // Optionally record a "Commission" log or transaction if needed for accounting
                // For now, implicit.
            }

            $order->update([
                'status' => 'completed',
                'delivery_status' => 'delivered'
            ]);
        });
    }

    /**
     * Refund Order (Buyer Dispute / Cancellation)
     */
    /**
     * Report an issue with the order (Opens a Dispute).
     * Does NOT refund automatically.
     */
    public function refundOrder(Order $order)
    {
        if ($order->status === 'completed') {
            throw new Exception("Impossible de signaler un problème sur une commande terminée.");
        }

        return DB::transaction(function () use ($order) {
            // DO NOT Refund automatically (Safe Mode)
            /*
            if ($order->payment_method === 'wallet') {
                $platformWallet = $this->getPlatformWallet();
                $buyerWallet = $order->buyer->wallet;

                $this->paymentService->transfer(
                    $platformWallet,
                    $buyerWallet,
                    $order->total_amount,
                    "Remboursement Commande #{$order->delivery_code}"
                );
            }
            */

            // Update status to 'dispute' (or 'problem')
            // We use 'dispute' for delivery_status to flag it.
            $order->update([
                // 'status' => 'cancelled', // Keep original status to not break flow completely
                'delivery_status' => 'dispute',
                'cancellation_reason' => 'Problème signalé par le client (En attente de résolution)'
            ]);
            
            // TODO: Notify Admin & Seller via Email/Notification
        });
    }

    /**
     * Admin Force Refund (Reverses transaction and restores stock)
     */
    public function adminRefund(Order $order)
    {
        if ($order->status === 'cancelled') {
             throw new Exception("Commande déjà annulée/remboursée.");
        }

        return DB::transaction(function () use ($order) {
            
            // 1. Refund Payment (if Wallet)
            if ($order->payment_method === 'wallet') {
                $platformWallet = $this->getPlatformWallet();
                $buyerWallet = $order->buyer->wallet;

                if (!$platformWallet || !$buyerWallet) {
                    throw new Exception("Portefeuille introuvable pour le remboursement.");
                }

                $this->paymentService->transfer(
                    $platformWallet,
                    $buyerWallet,
                    $order->total_amount,
                    "Remboursement Admin Commande #{$order->delivery_code}"
                );
            }

            // 2. Restore Stock
            foreach ($order->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }

            // 3. Update Status
            $order->update([
                'status' => 'cancelled',
                'delivery_status' => 'cancelled',
                'cancellation_reason' => 'Remboursement effectué par l\'administrateur.'
            ]);
        });
    }

    private function getPlatformWallet()
    {
        // Platform User = Super Admin (traorevetio22@gmail.com)
        $user = \App\Models\User::where('email', 'traorevetio22@gmail.com')->first();
        return $user ? $user->wallet : null;
    }
}
