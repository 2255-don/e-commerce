<?php

namespace Modules\Marketplace\ValueObjects;

use InvalidArgumentException;
use Illuminate\Support\Str;

/**
 * SKU Value Object
 * Stock Keeping Unit - Identifiant unique produit
 */
final class SKU
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Le SKU ne peut pas être vide');
        }
        
        if (!preg_match('/^[A-Z0-9-]+$/', $value)) {
            throw new InvalidArgumentException('Le SKU doit contenir uniquement des lettres majuscules, chiffres et tirets');
        }
    }
    
    public static function generate(string $prefix = 'PRD'): self
    {
        $timestamp = now()->format('Ymd');
        $random = strtoupper(Str::random(6));
        return new self("{$prefix}-{$timestamp}-{$random}");
    }
    
    public static function fromString(string $value): self
    {
        return new self(strtoupper($value));
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function equals(SKU $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
