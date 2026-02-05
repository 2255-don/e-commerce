<?php

namespace Modules\Seller\ValueObjects;

use InvalidArgumentException;

/**
 * Commission Rate Value Object
 * Taux de commission (0-100%)
 */
final class CommissionRate
{
    public function __construct(
        private readonly float $percentage
    ) {
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Le taux de commission doit être entre 0 et 100');
        }
    }
    
    public static function fromFloat(float $percentage): self
    {
        return new self($percentage);
    }
    
    public static function default(): self
    {
        return new self(10.0); // 10% par défaut
    }
    
    public function getPercentage(): float
    {
        return $this->percentage;
    }
    
    /**
     * Calculer la commission sur un montant
     */
    public function calculateCommission(float $amount): float
    {
        return ($amount * $this->percentage) / 100;
    }
    
    /**
     * Calculer le montant net après commission
     */
    public function calculateNetAmount(float $amount): float
    {
        return $amount - $this->calculateCommission($amount);
    }
    
    public function format(): string
    {
        return number_format($this->percentage, 2) . '%';
    }
    
    public function equals(CommissionRate $other): bool
    {
        return abs($this->percentage - $other->percentage) < 0.01;
    }
}
