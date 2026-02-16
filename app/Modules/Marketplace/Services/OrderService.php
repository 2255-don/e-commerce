<?php

namespace Modules\Marketplace\Services;

use Modules\Marketplace\Interfaces\OrderRepositoryInterface;
use Modules\Marketplace\Interfaces\CartRepositoryInterface;
use Modules\Marketplace\DTOs\PlaceOrderDTO;
use Modules\Marketplace\Entities\Order;
use Modules\Marketplace\ValueObjects\OrderStatus;
use Modules\Fintech\ValueObjects\TransactionReference;
use Modules\Fintech\Services\WalletService;
use Modules\Fintech\DTOs\TransferDTO;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly WalletService $walletService,
        private readonly StockService $stockService,
    ) {}
    
    public function placeOrder(PlaceOrderDTO $dto): Order
    {
        $cart = $this->cartRepository->findByUserId($dto->userId);
        
        if (!$cart || $cart->isEmpty()) {
            throw new \DomainException('Panier vide');
        }
        
        if (!$cart->validate()) {
            throw new \DomainException('Stock insuffisant pour certains produits');
        }
        
        return DB::transaction(function() use ($dto, $cart) {
            // Générer référence unique
            $reference = TransactionReference::generate('ORD')->getValue();
            
            // Créer la commande
            $order = $this->orderRepository->create([
                'buyer_id' => $dto->userId,
                'reference' => $reference,
                'total_amount' => $cart->getTotalAmount()->getAmount(),
                'status' => OrderStatus::PENDING->value,
                'payment_method' => $dto->paymentMethod,
                'payment_status' => 'pending',
                'notes' => $dto->notes,
            ]);
            
            // Créer les items de commande
            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'seller_id' => $cartItem->product->seller_id,
                    'quantity' => $cartItem->quantity,
                    'price_at_purchase' => $cartItem->price_at_addition,
                    'subtotal' => $cartItem->getTotal()->getAmount(),
                ]);
                
                // Diminuer le stock
                $this->stockService->reserveStock(
                    $cartItem->product_id,
                    $cartItem->quantity
                );
            }
            
            // Vider le panier
            $cart->clear();
            
            return $order->fresh(['items.product']);
        });
    }
    
    public function processPayment(string $orderId, string $method): Order
    {
        $order = $this->orderRepository->findById($orderId);
        
        if (!$order) {
            throw new \DomainException('Commande introuvable');
        }
        
        if ($method === 'wallet') {
            // Débiter le wallet de l'acheteur
            $totalAmount = $order->getTotalAmount()->getAmount();
            
            $this->walletService->debitForPayment(
                $order->buyer_id,
                $totalAmount,
                "Paiement commande {$order->reference}"
            );
            
            $order->markAsPaid();
            $order->markAsProcessing();
        }
        
        return $order;
    }
    
    public function completeOrder(string $orderId): Order
    {
        $order = $this->orderRepository->findById($orderId);
        
        if (!$order) {
            throw new \DomainException('Commande introuvable');
        }
        
        $order->markAsCompleted();
        
        return $order;
    }
    
    public function cancelOrder(string $orderId, string $reason): Order
    {
        $order = $this->orderRepository->findById($orderId);
        
        if (!$order) {
            throw new \DomainException('Commande introuvable');
        }
        
        return DB::transaction(function() use ($order, $reason) {
            // Libérer le stock
            foreach ($order->items as $item) {
                $this->stockService->releaseStock($item->product_id, $item->quantity);
            }
            
            $order->cancel($reason);
            
            return $order;
        });
    }
    
    public function getUserOrders(string $userId, int $perPage = 15)
    {
        return $this->orderRepository->getUserOrders($userId, $perPage);
    }
}
