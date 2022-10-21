<?php

namespace r\Options;

class FoldOptions
{
    public function __construct(
        public readonly \Closure|null $emit = null,
        public readonly \Closure|null $final_emit = null
    ) {
    }
}