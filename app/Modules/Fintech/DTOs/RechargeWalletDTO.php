<?php

namespace Modules\Fintech\DTOs;

/**
 * Recharge Wallet DTO
 */
final class RechargeWalletDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly float $amount,
        public readonly string $provider,
        public readonly string $phoneNumber,
        public readonly ?string $otp = null,
    ) {}
    
    public static function fromRequest(array $data, string $userId): self
    {
        return new self(
            userId: $userId,
            amount: (float) $data['amount'],
            provider: $data['provider'] ?? 'airtel',
            phoneNumber: $data['phone_number'],
            otp: $data['otp'] ?? null,
        );
    }
    
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'provider' => $this->provider,
            'phone_number' => $this->phoneNumber,
            'otp' => $this->otp,
        ];
    }
}
