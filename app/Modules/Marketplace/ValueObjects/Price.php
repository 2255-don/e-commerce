<?php

namespace Modules\Marketplace\ValueObjects;

use InvalidArgumentException;

/**
 * Price Value Object
 * Représente un prix avec opérations arithmétiques
 */
final class Price
{
    public function __construct(
        private readonly float $amount
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Le prix ne peut pas être négatif');
        }
    }
    
    public static function fromFloat(float $amount): self
    {
        return new self($amount);
    }
    
    public static function zero(): self
    {
        return new self(0.0);
    }
    
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    /**
     * Additionner deux prix
     */
    public function add(Price $other): self
    {
        return new self($this->amount + $other->amount);
    }
    
    /**
     * Soustraire un prix
     */
    public function subtract(Price $other): self
    {
        $result = $this->amount - $other->amount;
        if ($result < 0) {
            throw new InvalidArgumentException('Le résultat ne peut pas être négatif');
        }
        return new self($result);
    }
    
    /**
     * Multiplier par une quantité
     */
    public function multiply(int $quantity): self
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('La quantité ne peut pas être négative');
        }
        return new self($this->amount * $quantity);
    }
    
    /**
     * Appliquer une réduction en pourcentage
     */
    public function applyDiscount(float $percentage): self
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Le pourcentage doit être entre 0 et 100');
        }
        
        $discountAmount = ($this->amount * $percentage) / 100;
        return new self($this->amount - $discountAmount);
    }
    
    /**
     * Comparer deux prix
     */
    public function isGreaterThan(Price $other): bool
    {
        return $this->amount > $other->amount;
    }
    
    public function equals(Price $other): bool
    {
        return abs($this->amount - $other->amount) < 0.01;
    }
    
    /**
     * Format pour affichage
     */
    public function format(string $currency = 'FCFA'): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' ' . $currency;
    }
    
    /**
     * Format décimal
     */
    public function toDecimal(): string
    {
        return number_format($this->amount, 2, '.', '');
    }
}
