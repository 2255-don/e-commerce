<?php

namespace Modules\Fintech\ValueObjects;

use InvalidArgumentException;

/**
 * Currency Value Object
 */
final class Currency
{
    public const FCFA = 'FCFA';
    public const USD = 'USD';
    public const EUR = 'EUR';
    
    private const VALID_CURRENCIES = [
        self::FCFA,
        self::USD,
        self::EUR,
    ];
    
    public function __construct(
        private readonly string $value
    ) {
        if (!in_array($value, self::VALID_CURRENCIES)) {
            throw new InvalidArgumentException("Devise invalide: {$value}");
        }
    }
    
    public static function fcfa(): self
    {
        return new self(self::FCFA);
    }
    
    public static function usd(): self
    {
        return new self(self::USD);
    }
    
    public static function eur(): self
    {
        return new self(self::EUR);
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function equals(Currency $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function isFcfa(): bool
    {
        return $this->value === self::FCFA;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
