<?php

namespace Modules\Fintech\ValueObjects;

use InvalidArgumentException;

/**
 * Amount Value Object
 * Représente un montant financier avec validation
 */
final class Amount
{
    public function __construct(
        private readonly float $value
    ) {
        if ($value < 0) {
            throw new InvalidArgumentException('Le montant ne peut pas être négatif');
        }
    }
    
    public static function fromFloat(float $value): self
    {
        return new self($value);
    }
    
    public static function zero(): self
    {
        return new self(0.0);
    }
    
    public function getValue(): float
    {
        return $this->value;
    }
    
    /**
     * Additionne deux montants
     */
    public function add(Amount $other): self
    {
        return new self($this->value + $other->value);
    }
    
    /**
     * Soustrait un montant
     */
    public function subtract(Amount $other): self
    {
        $result = $this->value - $other->value;
        if ($result < 0) {
            throw new InvalidArgumentException('Le résultat ne peut pas être négatif');
        }
        return new self($result);
    }
    
    /**
     * Vérifie si ce montant est supérieur à un autre
     */
    public function isGreaterThan(Amount $other): bool
    {
        return $this->value > $other->value;
    }
    
    /**
     * Vérifie si ce montant est égal à un autre
     */
    public function equals(Amount $other): bool
    {
        return abs($this->value - $other->value) < 0.01; // Tolérance pour float
    }
    
    /**
     * Vérifie si suffisant pour couvrir un autre montant
     */
    public function canCover(Amount $other): bool
    {
        return $this->isGreaterThan($other) || $this->equals($other);
    }
    
    /**
     * Format pour affichage
     */
    public function format(string $currency = 'FCFA'): string
    {
        return number_format($this->value, 0, ',', ' ') . ' ' . $currency;
    }
    
    /**
     * Format décimal
     */
    public function toDecimal(): string
    {
        return number_format($this->value, 2, '.', '');
    }
}
