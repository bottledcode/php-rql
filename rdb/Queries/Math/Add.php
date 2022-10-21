<?php

namespace r\Queries\Math;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Add extends BinaryOp
{
    public function __construct(string|int|float|array|Query $value, string|int|float|array|Query $other)
    {
        parent::__construct(TermTermType::PB_ADD, $value, $other);
    }
}
