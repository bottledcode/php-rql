<?php

namespace r\Queries\Joins;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class OuterJoin extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, ValuedQuery $otherSequence, callable|Query $predicate)
    {
        $predicate = $this->nativeToFunction($predicate);
        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $otherSequence);
        $this->setPositionalArg(2, $predicate);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_OUTER_JOIN;
    }
}
