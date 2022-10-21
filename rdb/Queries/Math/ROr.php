<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;
use r\Query;

class ROr extends BinaryOp
{
    public function __construct(bool|Query $value, bool|Query $other)
    {
        parent::__construct(TermTermType::PB_OR, $value, $other);
    }
}
