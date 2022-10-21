<?php

namespace r\Queries\Control;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Range extends ValuedQuery
{
    public function __construct(int|Query $startOrEndValue = null, int|Query $endValue = null)
    {
        if (isset($startOrEndValue)) {
            $this->setPositionalArg(0, $this->nativeToDatum($startOrEndValue));
            if (isset($endValue)) {
                $this->setPositionalArg(1, $this->nativeToDatum($endValue));
            }
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_RANGE;
    }
}
