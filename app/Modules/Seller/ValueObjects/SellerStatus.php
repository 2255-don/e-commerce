<?php

namespace Modules\Seller\ValueObjects;

/**
 * Seller Status Enum
 */
enum SellerStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case SUSPENDED = 'suspended';
    case REJECTED = 'rejected';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::APPROVED => 'Approuvé',
            self::SUSPENDED => 'Suspendu',
            self::REJECTED => 'Rejeté',
        };
    }
    
    public function canSell(): bool
    {
        return $this === self::APPROVED;
    }
    
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }
    
    public function isActive(): bool
    {
        return $this === self::APPROVED;
    }
    
    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'badge-status-pending',
            self::APPROVED => 'badge-status-active',
            self::SUSPENDED => 'badge-brand-grey',
            self::REJECTED => 'badge-status-inactive',
        };
    }
}
