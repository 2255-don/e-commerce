<?php

namespace Modules\Identity\ValueObjects;

/**
 * UserStatus Value Object
 * 
 * Représente le statut d'un utilisateur (active, inactive, suspended)
 */
final class UserStatus
{
    private const ACTIVE = 'active';
    private const INACTIVE = 'inactive';
    private const SUSPENDED = 'suspended';

    private string $value;

    private function __construct(string $value)
    {
        $allowed = [self::ACTIVE, self::INACTIVE, self::SUSPENDED];
        
        if (!in_array($value, $allowed, true)) {
            throw new \InvalidArgumentException(
                "Invalid user status: {$value}. Allowed: " . implode(', ', $allowed)
            );
        }
        
        $this->value = $value;
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function inactive(): self
    {
        return new self(self::INACTIVE);
    }

    public static function suspended(): self
    {
        return new self(self::SUSPENDED);
    }

    public static function fromString(string $status): self
    {
        return new self($status);
    }

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->value === self::INACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->value === self::SUSPENDED;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
