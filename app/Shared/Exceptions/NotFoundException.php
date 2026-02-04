<?php

namespace Shared\Exceptions;

class NotFoundException extends DomainException
{
    public function __construct(string $resource = "Resource", int $code = 404)
    {
        parent::__construct("{$resource} not found", $code);
    }
}
