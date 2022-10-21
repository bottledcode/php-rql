<?php

namespace r\Queries\Control;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class RForeach extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, callable|Query $queryFunction)
    {
        $queryFunction = $this->nativeToFunction($queryFunction);
        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $queryFunction);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_FOR_EACH;
    }
}
