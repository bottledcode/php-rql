<?php

namespace r\Queries\Transformations;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Nth extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, int|Query $index)
    {
        $index = $this->nativeToDatum($index);

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $index);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_NTH;
    }
}
