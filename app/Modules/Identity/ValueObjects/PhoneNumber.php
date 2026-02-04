<?php

namespace Modules\Identity\ValueObjects;

/**
 * PhoneNumber Value Object
 * 
 * Valide et normalise les numéros de téléphone
 */
final class PhoneNumber
{
    private string $value;

    private function __construct(string $value)
    {
        // Nettoyer le numéro (enlever espaces, tirets, parenthèses)
        $cleaned = preg_replace('/[\s\-\(\)]/', '', $value);
        
        // Valider format simple (10-15 chiffres, peut commencer par +)
        if (!preg_match('/^\+?\d{10,15}$/', $cleaned)) {
            throw new \InvalidArgumentException("Invalid phone number: {$value}");
        }
        
        $this->value = $cleaned;
    }

    public static function fromString(string $phone): self
    {
        return new self($phone);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function formatted(): string
    {
        // Format simple pour affichage
        // Ex: +243123456789 → +243 123 456 789
        if (str_starts_with($this->value, '+')) {
            $country = substr($this->value, 0, 4);
            $rest = substr($this->value, 4);
            return $country . ' ' . chunk_split($rest, 3, ' ');
        }
        
        return chunk_split($this->value, 3, ' ');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
