<?php

namespace Modules\Marketplace\ValueObjects;

/**
 * Order Status Enum
 */
enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::PROCESSING => 'En traitement',
            self::COMPLETED => 'Complétée',
            self::CANCELLED => 'Annulée',
            self::REFUNDED => 'Remboursée',
        };
    }
    
    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED, self::REFUNDED]);
    }
    
    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSING]);
    }
    
    public function canBeRefunded(): bool
    {
        return $this === self::COMPLETED;
    }
    
    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'badge-status-pending',
            self::PROCESSING => 'badge-brand-outline',
            self::COMPLETED => 'badge-status-active',
            self::CANCELLED => 'badge-status-inactive',
            self::REFUNDED => 'badge-brand-grey',
        };
    }
}
