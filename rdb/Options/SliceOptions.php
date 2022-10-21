<?php

namespace r\Options;

class SliceOptions
{
    public function __construct(
        public readonly string|null $left_bound = null,
        public readonly string|null $right_bound = null
    ) {
    }
}