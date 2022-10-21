<?php

namespace r\Queries\Aggregations;

use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Count extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, mixed $filter = null)
    {
        $this->setPositionalArg(0, $sequence);
        if (isset($filter)) {
            $this->setPositionalArg(1, $this->nativeToDatumOrFunction($filter));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_COUNT;
    }
}
