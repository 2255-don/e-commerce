<?php

namespace Modules\Seller\DTOs;

final class CreateSellerProfileDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly string $shopName,
        public readonly string $businessName,
        public readonly ?float $commissionRate = null,
    ) {}
    
    public static function fromRequest(array $data, string $userId): self
    {
        return new self(
            userId: $userId,
            shopName: $data['shop_name'],
            businessName: $data['business_name'],
            commissionRate: isset($data['commission_rate']) ? (float) $data['commission_rate'] : null,
        );
    }
    
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'shop_name' => $this->shopName,
            'business_name' => $this->businessName,
            'commission_rate' => $this->commissionRate ?? 10.0,
            'status' => 'pending',
            'is_active' => false,
        ];
    }
}
