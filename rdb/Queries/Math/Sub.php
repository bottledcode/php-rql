<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;
use r\Query;

class Sub extends BinaryOp
{
    public function __construct(int|float|Query $value, int|float|Query|\DateTimeInterface $other)
    {
        parent::__construct(TermTermType::PB_SUB, $value, $other);
    }
}
