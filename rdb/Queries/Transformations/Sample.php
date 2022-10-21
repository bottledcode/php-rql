<?php

namespace r\Queries\Transformations;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Sample extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, int|Query $n)
    {
        $n = $this->nativeToDatum($n);

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $n);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_SAMPLE;
    }
}
