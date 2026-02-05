<?php

namespace Modules\Marketplace\Services;

use Modules\Marketplace\ValueObjects\Price;
use Modules\Marketplace\Entities\Cart;

class PricingService
{
    public function calculateProductPrice(float $unitPrice, int $quantity): Price
    {
        $price = Price::fromFloat($unitPrice);
        return $price->multiply($quantity);
    }
    
    public function calculateCartTotal(Cart $cart): Price
    {
        return $cart->getTotalAmount();
    }
    
    public function applyDiscounts(Price $price, array $discounts): Price
    {
        $finalPrice = $price;
        
        foreach ($discounts as $discount) {
            if (isset($discount['percentage'])) {
                $finalPrice = $finalPrice->applyDiscount($discount['percentage']);
            }
        }
        
        return $finalPrice;
    }
}
