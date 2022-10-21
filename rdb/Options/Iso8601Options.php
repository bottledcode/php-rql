<?php

namespace r\Options;

class Iso8601Options
{
    public function __construct(public readonly string|null $defaultTimezone = null)
    {
    }
}