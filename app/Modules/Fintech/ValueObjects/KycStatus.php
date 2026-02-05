<?php

namespace Modules\Fintech\ValueObjects;

/**
 * KYC Status Enum
 */
enum KycStatus: string
{
    case NONE = 'none';
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    
    public function label(): string
    {
        return match($this) {
            self::NONE => 'Non soumis',
            self::PENDING => 'En attente',
            self::VERIFIED => 'Vérifié',
            self::REJECTED => 'Rejeté',
        };
    }
    
    public function isVerified(): bool
    {
        return $this === self::VERIFIED;
    }
    
    public function canSubmit(): bool
    {
        return in_array($this, [self::NONE, self::REJECTED]);
    }
    
    public function badgeClass(): string
    {
        return match($this) {
            self::NONE => 'badge-brand-grey',
            self::PENDING => 'badge-status-pending',
            self::VERIFIED => 'badge-status-active',
            self::REJECTED => 'badge-status-inactive',
        };
    }
}
