<?php

namespace r\Queries\Math;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class RqlMatch extends ValuedQuery
{
    public function __construct(ValuedQuery $value, string|Query $expression)
    {
        $expression = $this->nativeToDatum($expression);

        $this->setPositionalArg(0, $value);
        $this->setPositionalArg(1, $expression);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_MATCH;
    }
}
