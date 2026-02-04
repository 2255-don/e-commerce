<?php

namespace Modules\Identity\ValueObjects;

/**
 * Email Value Object
 * 
 * Valide et normalise les adresses email
 */
final class Email
{
    private string $value;

    private function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email address: {$value}");
        }
        
        // Normaliser (lowercase)
        $this->value = strtolower(trim($value));
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function domain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }

    public function localPart(): string
    {
        return substr($this->value, 0, strpos($this->value, '@'));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
