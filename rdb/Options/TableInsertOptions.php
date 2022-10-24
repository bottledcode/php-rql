<?php

namespace r\Options;

class TableInsertOptions
{
    public function __construct(
        public readonly Durability|null $durability = null,
        public readonly string|bool|null $return_changes = null,
        public readonly \Closure|string|null $conflict = null,
        public readonly bool|null $ignore_write_hook = null,
    ) {
    }
}