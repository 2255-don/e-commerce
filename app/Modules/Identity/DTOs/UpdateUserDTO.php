<?php

namespace Modules\Identity\DTOs;

class UpdateUserDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $password = null,
    ) {}
    
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            password: $data['password'] ?? null,
        );
    }
    
    public function toArray(): array
    {
        $data = [];
        
        if ($this->name !== null) $data['name'] = $this->name;
        if ($this->email !== null) $data['email'] = $this->email;
        if ($this->phone !== null) $data['phone'] = $this->phone;
        if ($this->password !== null) $data['password'] = bcrypt($this->password);
        
        return $data;
    }
}
