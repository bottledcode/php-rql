<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;

class Lt extends BinaryOp
{
    public function __construct(mixed $value, mixed $other)
    {
        parent::__construct(TermTermType::PB_LT, $value, $other);
    }
}
