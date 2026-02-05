<?php

namespace Modules\Marketplace\Repositories;

use Modules\Marketplace\Entities\Order;
use Modules\Marketplace\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function findById(string $id): ?Order
    {
        return Order::with(['items.product', 'items.seller', 'buyer'])->find($id);
    }
    
    public function findByReference(string $reference): ?Order
    {
        return Order::with(['items.product'])->where('reference', $reference)->first();
    }
    
    public function getUserOrders(string $userId, int $perPage = 15)
    {
        return Order::where('buyer_id', $userId)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getSellerOrders(string $sellerId, int $perPage = 15)
    {
        return Order::whereHas('items', function($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })
        ->with(['items.product', 'buyer'])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }
    
    public function create(array $data): Order
    {
        return Order::create($data);
    }
}
