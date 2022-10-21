<?php

namespace r\Options;

class BetweenOptions {
    public function __construct(
        public readonly string|null $index = null,
        public readonly string|null $left_bound = null,
        public readonly string|null $right_bound = null,
    ) {
    }
}