<?php

namespace Modules\Fintech\DTOs;

use Modules\Fintech\ValueObjects\TransactionType;
use Modules\Fintech\ValueObjects\TransactionStatus;

/**
 * Transaction DTO
 */
final class TransactionDTO
{
    public function __construct(
        public readonly ?string $senderWalletId,
        public readonly ?string $receiverWalletId,
        public readonly string $type,
        public readonly float $amount,
        public readonly string $reference,
        public readonly ?string $description = null,
        public readonly string $status = 'pending',
        public readonly ?array $metadata = null,
    ) {}
    
    public static function create(
        ?string $senderWalletId,
        ?string $receiverWalletId,
        TransactionType $type,
        float $amount,
        string $reference,
        ?string $description = null,
    ): self {
        return new self(
            senderWalletId: $senderWalletId,
            receiverWalletId: $receiverWalletId,
            type: $type->value,
            amount: $amount,
            reference: $reference,
            description: $description,
            status: TransactionStatus::PENDING->value,
        );
    }
    
    public function toArray(): array
    {
        return [
            'sender_wallet_id' => $this->senderWalletId,
            'receiver_wallet_id' => $this->receiverWalletId,
            'type' => $this->type,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'description' => $this->description,
            'status' => $this->status,
            'metadata' => $this->metadata,
        ];
    }
}
