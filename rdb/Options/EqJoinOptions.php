<?php

namespace r\Options;

class EqJoinOptions
{
    public function __construct(public readonly string|null $index = null, public readonly bool|null $ordered = null)
    {
    }
}