<?php

namespace Modules\Identity\ValueObjects;

/**
 * FeatureName Value Object
 * 
 * Représente le nom d'une feature au format: module.entity.action
 * Exemples: admin.roles.create, seller.products.edit
 */
final class FeatureName
{
    private string $value;
    private string $module;
    private string $entity;
    private string $action;

    private function __construct(string $value)
    {
        // Valider format: module.entity.action
        if (!preg_match('/^[a-z]+\.[a-z\-]+\.[a-z\-]+$/', $value)) {
            throw new \InvalidArgumentException(
                "Invalid feature name format: {$value}. Expected format: module.entity.action"
            );
        }
        
        $parts = explode('.', $value);
        
        $this->value = $value;
        $this->module = $parts[0];
        $this->entity = $parts[1];
        $this->action = $parts[2];
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public static function fromParts(string $module, string $entity, string $action): self
    {
        return new self("{$module}.{$entity}.{$action}");
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function module(): string
    {
        return $this->module;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function belongsToModule(string $moduleName): bool
    {
        return $this->module === $moduleName;
    }

    public function isAction(string $actionName): bool
    {
        return $this->action === $actionName;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
