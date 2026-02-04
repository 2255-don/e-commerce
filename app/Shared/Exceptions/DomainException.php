<?php

namespace Shared\Exceptions;

/**
 * Base exception for all domain-level exceptions
 */
abstract class DomainException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
