<?php

namespace Modules\Fintech\ValueObjects;

/**
 * Transaction Status Enum
 */
enum TransactionStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::PROCESSING => 'En traitement',
            self::COMPLETED => 'Complété',
            self::FAILED => 'Échoué',
            self::CANCELLED => 'Annulé',
        };
    }
    
    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED, self::CANCELLED]);
    }
    
    public function isProcessable(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSING]);
    }
    
    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'badge-status-pending',
            self::PROCESSING => 'badge-brand-outline',
            self::COMPLETED => 'badge-status-active',
            self::FAILED => 'badge-status-inactive',
            self::CANCELLED => 'badge-brand-grey',
        };
    }
}
