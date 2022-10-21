<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;
use r\Query;

class Div extends BinaryOp
{
    public function __construct(int|float|Query $value, int|float|Query $other)
    {
        parent::__construct(TermTermType::PB_DIV, $value, $other);
    }
}
