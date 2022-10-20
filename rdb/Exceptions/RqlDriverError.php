<?php

namespace r\Exceptions;

class RqlDriverError extends RqlException
{
    public function __construct(string $message, int $code = 0, \Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return "RqlDriverError:\n  " . $this->getMessage() . "\n";
    }
}
