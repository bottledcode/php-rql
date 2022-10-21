<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;
use r\Query;

class RAnd extends BinaryOp
{
    public function __construct(bool|Query $value, bool|Query $other)
    {
        parent::__construct(TermTermType::PB_AND, $value, $other);
    }
}
