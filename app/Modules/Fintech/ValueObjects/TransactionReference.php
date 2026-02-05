<?php

namespace Modules\Fintech\ValueObjects;

use InvalidArgumentException;
use Illuminate\Support\Str;

/**
 * Transaction Reference Value Object
 * Génère et valide les références de transaction
 */
final class TransactionReference
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('La référence de transaction ne peut pas être vide');
        }
    }
    
    public static function generate(string $prefix = 'TXN'): self
    {
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(6));
        return new self("{$prefix}-{$timestamp}-{$random}");
    }
    
    public static function fromString(string $value): self
    {
        return new self($value);
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function equals(TransactionReference $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
