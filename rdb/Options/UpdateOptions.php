<?php

namespace r\Options;

class UpdateOptions
{
    public function __construct(
        public readonly Durability|null $durability = null,
        public readonly string|bool|null $return_changes = null,
        public readonly bool|null $non_atomic = null,
        public readonly bool|null $ignore_write_hook = null,
    ) {
    }
}