<?php

namespace r\Options;

class DeleteOptions
{
    public function __construct(
        public readonly Durability|null $durability = null,
        public readonly bool|string|null $return_changes = null,
        public readonly bool|null $ignore_write_hook = null,
    ) {
    }
}