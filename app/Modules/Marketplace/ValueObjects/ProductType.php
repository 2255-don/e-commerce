<?php

namespace Modules\Marketplace\ValueObjects;

/**
 * Product Type Enum
 */
enum ProductType: string
{
    case PHYSICAL = 'physical';
    case DIGITAL = 'digital';
    case SERVICE = 'service';
    
    public function label(): string
    {
        return match($this) {
            self::PHYSICAL => 'Produit Physique',
            self::DIGITAL => 'Produit Numérique',
            self::SERVICE => 'Service',
        };
    }
    
    public function requiresShipping(): bool
    {
        return $this === self::PHYSICAL;
    }
    
    public function requiresStock(): bool
    {
        return in_array($this, [self::PHYSICAL, self::DIGITAL]);
    }
}
