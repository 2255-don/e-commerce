<?php

namespace Shared\Exceptions;

class UnauthorizedException extends DomainException
{
    public function __construct(string $message = "Unauthorized access", int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
