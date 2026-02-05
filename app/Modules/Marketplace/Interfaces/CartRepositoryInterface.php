<?php

namespace Modules\Marketplace\Interfaces;

use Modules\Marketplace\Entities\Cart;

interface CartRepositoryInterface
{
    public function findByUserId(string $userId): ?Cart;
    public function getOrCreateForUser(string $userId): Cart;
    public function clear(string $cartId): void;
}
