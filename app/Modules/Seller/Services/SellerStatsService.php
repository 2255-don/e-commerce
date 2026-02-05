<?php

namespace Modules\Seller\Services;

use Modules\Marketplace\Interfaces\ProductRepositoryInterface;
use Modules\Marketplace\Interfaces\OrderRepositoryInterface;
use Modules\Seller\Interfaces\SellerProfileRepositoryInterface;

class SellerStatsService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SellerProfileRepositoryInterface $sellerProfileRepository,
    ) {}
    
    public function getStats(string $sellerId): array
    {
        $sellerProfile = $this->sellerProfileRepository->findByUserId($sellerId);
        
        if (!$sellerProfile) {
            return [
                'total_products' => 0,
                'total_orders' => 0,
                'total_revenue' => 0,
                'commission_earned' => 0,
            ];
        }
        
        // Nombre de produits
        $totalProducts = $this->productRepository->getBySeller($sellerId, 9999)->total();
        
        // Commandes et revenus
        $orders = $this->orderRepository->getSellerOrders($sellerId, 9999);
        $totalOrders = $orders->total();
        
        $totalRevenue = $orders->sum(function($order) {
            return $order->items->where('seller_id', $order->buyer_id)->sum('subtotal');
        });
        
        // Commission calculée
        $commissionEarned = $sellerProfile->calculateCommission($totalRevenue);
        
        return [
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'commission_earned' => $commissionEarned,
            'net_revenue' => $totalRevenue - $commissionEarned,
        ];
    }
}
