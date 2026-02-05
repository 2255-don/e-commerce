<?php

namespace Modules\Marketplace\ValueObjects;

use InvalidArgumentException;

/**
 * Stock Value Object
 * Gestion de stock avec validations
 */
final class Stock
{
    public function __construct(
        private readonly int $quantity
    ) {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Le stock ne peut pas être négatif');
        }
    }
    
    public static function fromInt(int $quantity): self
    {
        return new self($quantity);
    }
    
    public static function zero(): self
    {
        return new self(0);
    }
    
    public function getQuantity(): int
    {
        return $this->quantity;
    }
    
    /**
     * Diminuer le stock
     */
    public function decrease(int $amount): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Le montant ne peut pas être négatif');
        }
        
        $newQuantity = $this->quantity - $amount;
        if ($newQuantity < 0) {
            throw new InvalidArgumentException('Stock insuffisant');
        }
        
        return new self($newQuantity);
    }
    
    /**
     * Augmenter le stock
     */
    public function increase(int $amount): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Le montant ne peut pas être négatif');
        }
        
        return new self($this->quantity + $amount);
    }
    
    /**
     * Vérifier si stock disponible
     */
    public function isAvailable(int $requested): bool
    {
        return $this->quantity >= $requested;
    }
    
    /**
     * Vérifier si stock faible
     */
    public function isLow(int $threshold = 10): bool
    {
        return $this->quantity <= $threshold;
    }
    
    /**
     * Vérifier si en rupture
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity === 0;
    }
    
    /**
     * Obtenir le statut
     */
    public function getStatus(): string
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        }
        
        if ($this->isLow()) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }
    
    /**
     * Badge CSS class pour UI
     */
    public function getBadgeClass(): string
    {
        return match($this->getStatus()) {
            'out_of_stock' => 'badge-status-inactive',
            'low_stock' => 'badge-status-pending',
            'in_stock' => 'badge-status-active',
        };
    }
}
