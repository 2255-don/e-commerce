<?php

namespace Modules\Seller\ValueObjects;

use InvalidArgumentException;
use Illuminate\Support\Str;

/**
 * License Number Value Object
 * Numéro de licence unique pour vendeur
 */
final class LicenseNumber
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Le numéro de licence ne peut pas être vide');
        }
        
        if (!$this->isValid()) {
            throw new InvalidArgumentException('Format de licence invalide');
        }
    }
    
    public static function generate(): self
    {
        $timestamp = now()->format('Ymd');
        $random = strtoupper(Str::random(8));
        return new self("LIC-{$timestamp}-{$random}");
    }
    
    public static function fromString(string $value): self
    {
        return new self(strtoupper($value));
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function isValid(): bool
    {
        // Format: LIC-YYYYMMDD-XXXXXXXX
        return preg_match('/^LIC-\d{8}-[A-Z0-9]{8}$/', $this->value) === 1;
    }
    
    public function equals(LicenseNumber $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
