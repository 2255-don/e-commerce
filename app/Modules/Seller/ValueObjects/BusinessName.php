<?php

namespace Modules\Seller\ValueObjects;

use InvalidArgumentException;

/**
 * Business Name Value Object
 * Nom entreprise avec validation
 */
final class BusinessName
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Le nom de l\'entreprise ne peut pas être vide');
        }
        
        if (strlen($value) < 3) {
            throw new InvalidArgumentException('Le nom de l\'entreprise doit avoir au moins 3 caractères');
        }
        
        if (strlen($value) > 100) {
            throw new InvalidArgumentException('Le nom de l\'entreprise ne peut pas dépasser 100 caractères');
        }
    }
    
    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function equals(BusinessName $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
