<?php

namespace Modules\Seller\DTOs;

final class UpdateSellerProfileDTO
{
    public function __construct(
        public readonly ?string $shopName = null,
        public readonly ?string $businessName = null,
        public readonly ?float $commissionRate = null,
    ) {}
    
    public static function fromRequest(array $data): self
    {
        return new self(
            shopName: $data['shop_name'] ?? null,
            businessName: $data['business_name'] ?? null,
            commissionRate: isset($data['commission_rate']) ? (float) $data['commission_rate'] : null,
        );
    }
    
    public function toArray(): array
    {
        $data = [];
        
        if ($this->shopName !== null) $data['shop_name'] = $this->shopName;
        if ($this->businessName !== null) $data['business_name'] = $this->businessName;
        if ($this->commissionRate !== null) $data['commission_rate'] = $this->commissionRate;
        
        return $data;
    }
}
