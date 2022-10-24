<?php

namespace r\Options;

class GetAllOptions
{
    public function __construct(public readonly string|null $index = null)
    {
    }
}