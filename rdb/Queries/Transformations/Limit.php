<?php

namespace r\Queries\Transformations;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Limit extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, int|Query $n)
    {
        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $this->nativeToDatum($n));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_LIMIT;
    }
}
