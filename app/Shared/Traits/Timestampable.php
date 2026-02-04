<?php

namespace Shared\Traits;

/**
 * Shared trait for timestamp handling
 */
trait Timestampable
{
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
