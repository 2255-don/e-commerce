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
    /**
     * Process checkout from cart.
     */
    public function processCheckout($user, array $cartItems, $totalAmount, $paymentMethod = 'wallet')
    {
        return DB::transaction(function () use ($user, $cartItems, $totalAmount, $paymentMethod) {
            
            // 1. Validate Balance IF wallet
            if ($paymentMethod === 'wallet') {
                $wallet = $user->wallet;
                if (!$wallet || $wallet->balance < $totalAmount) {
                    throw new Exception("Solde insuffisant. Veuillez recharger votre portefeuille.");
                }

                // Deduct from Buyer
                $wallet->decrement('balance', $totalAmount);
                
                // Record Transaction
                Transaction::create([
                    'sender_wallet_id' => $wallet->id,
                    'receiver_wallet_id' => null, 
                    'type' => 'payment',
                    'amount' => $totalAmount,
                    'reference' => 'PAY-' . strtoupper(Str::random(10)),
                    'description' => 'Achat MarketPlace',
                    'status' => 'completed'
                ]);
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
                    $orderStatus = 'pending_payment'; // Waiting for payment confirmation
                    $deliveryStatus = 'pending';
                } else {
                    $orderStatus = 'pending'; // Paid, waiting for delivery
                    $deliveryStatus = 'pending';
                }

                $order = Order::create([
                    'buyer_id' => $user->id,
                    'total_amount' => $orderTotal,
                    'status' => $orderStatus,
                    'payment_method' => $paymentMethod,
                    'delivery_status' => $deliveryStatus,
                    'delivery_code' => strtoupper(Str::random(6))
                ]);

                // 4. Create Order Items & Update Stock
                foreach ($items as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['id'], // Assuming ID is passed
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['price']
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
    }
}
