<?php

namespace Modules\Fintech\ValueObjects;

/**
 * Transaction Type Enum
 */
enum TransactionType: string
{
    case RECHARGE = 'recharge';
    case TRANSFER = 'transfer';
    case PAYMENT = 'payment';
    case REFUND = 'refund';
    case WITHDRAWAL = 'withdrawal';
    
    public function label(): string
    {
        return match($this) {
            self::RECHARGE => 'Rechargement',
            self::TRANSFER => 'Transfert',
            self::PAYMENT => 'Paiement',
            self::REFUND => 'Remboursement',
            self::WITHDRAWAL => 'Retrait',
        };
    }
    
    public function isCredit(): bool
    {
        return in_array($this, [self::RECHARGE, self::REFUND]);
    }
    
    public function isDebit(): bool
    {
        return in_array($this, [self::TRANSFER, self::PAYMENT, self::WITHDRAWAL]);
    }
}
