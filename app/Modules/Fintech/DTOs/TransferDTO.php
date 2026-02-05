<?php

namespace Modules\Fintech\DTOs;

/**
 * Transfer DTO
 */
final class TransferDTO
{
    public function __construct(
        public readonly string $senderWalletId,
        public readonly string $receiverWalletId,
        public readonly float $amount,
        public readonly ?string $description = null,
    ) {}
    
    public static function create(
        string $senderWalletId,
        string $receiverWalletId,
        float $amount,
        ?string $description = null
    ): self {
        return new self($senderWalletId, $receiverWalletId, $amount, $description);
    }
    
    public function toArray(): array
    {
        return [
            'sender_wallet_id' => $this->senderWalletId,
            'receiver_wallet_id' => $this->receiverWalletId,
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }
}
