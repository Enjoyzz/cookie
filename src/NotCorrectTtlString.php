<?php

namespace Enjoys\Cookie;

use Throwable;

class NotCorrectTtlString extends \Exception
{
    public function __construct(string $ttl, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('strtotime() failed to convert string "%s" to timestamp', $ttl), $code, $previous);
    }
}

