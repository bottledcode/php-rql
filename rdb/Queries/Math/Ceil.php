<?php

namespace r\Queries\Math;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Ceil extends ValuedQuery
{
    public function __construct(float|int|Query $value)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($value));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_CEIL;
    }
}
