<?php

namespace Shared\ValueObjects;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Value Object for UUID handling
 */
final class Uuid
{
    private string $value;

    private function __construct(string $value)
    {
        if (!RamseyUuid::isValid($value)) {
            throw new \InvalidArgumentException("Invalid UUID: {$value}");
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public static function fromString(string $uuid): self
    {
        return new self($uuid);
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
