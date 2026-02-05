<?php

namespace Modules\Identity\DTOs;

class AssignRoleDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly array $roleIds,
    ) {}
    
    public static function fromRequest(string $userId, array $data): self
    {
        return new self(
            userId: $userId,
            roleIds: $data['role_ids'] ?? [],
        );
    }
}
