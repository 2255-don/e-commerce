<?php

namespace Modules\Marketplace\ValueObjects;

use InvalidArgumentException;
use Illuminate\Support\Str;

/**
 * Slug Value Object
 * URL-friendly identifier
 */
final class Slug
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Le slug ne peut pas être vide');
        }
        
        if (!preg_match('/^[a-z0-9-]+$/', $value)) {
            throw new InvalidArgumentException('Le slug doit contenir uniquement des lettres minuscules, chiffres et tirets');
        }
    }
    
    public static function fromString(string $value): self
    {
        $slug = Str::slug($value);
        return new self($slug);
    }
    
    public static function generate(string $text, ?string $suffix = null): self
    {
        $slug = Str::slug($text);
        
        if ($suffix) {
            $slug .= '-' . $suffix;
        }
        
        return new self($slug);
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function equals(Slug $other): bool
    {
        return $this->value === $other->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
