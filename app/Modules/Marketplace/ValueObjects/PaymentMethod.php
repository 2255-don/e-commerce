<?php

namespace Modules\Marketplace\ValueObjects;

/**
 * Payment Method Enum
 */
enum PaymentMethod: string
{
    case WALLET = 'wallet';
    case CASH_ON_DELIVERY = 'cash_on_delivery';
    case MOBILE_MONEY = 'mobile_money';
    
    public function label(): string
    {
        return match($this) {
            self::WALLET => 'Portefeuille',
            self::CASH_ON_DELIVERY => 'Paiement à la livraison',
            self::MOBILE_MONEY => 'Mobile Money',
        };
    }
    
    public function requiresPrePayment(): bool
    {
        return in_array($this, [self::WALLET, self::MOBILE_MONEY]);
    }
    
    public function icon(): string
    {
        return match($this) {
            self::WALLET => 'bx-wallet',
            self::CASH_ON_DELIVERY => 'bx-money',
            self::MOBILE_MONEY => 'bx-mobile',
        };
    }
}
