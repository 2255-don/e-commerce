<?php

namespace Modules\Marketplace\Interfaces;

use Modules\Marketplace\Entities\Order;

interface OrderRepositoryInterface
{
    public function findById(string $id): ?Order;
    public function findByReference(string $reference): ?Order;
    public function getUserOrders(string $userId, int $perPage = 15);
    public function getSellerOrders(string $sellerId, int $perPage = 15);
    public function create(array $data): Order;
}
