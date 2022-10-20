<?php

namespace r\Options;

class TableOptions
{
    public function __construct(
        public readonly ReadMode|null $readMode = null,
        public readonly IdentifierFormat|null $identifierFormat = null
    ) {
    }
}