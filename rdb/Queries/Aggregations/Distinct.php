<?php

namespace r\Queries\Aggregations;

use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Distinct extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, ...$opts)
    {
        $this->setPositionalArg(0, $sequence);
        foreach ($opts as $opt => $val) {
            $this->setOptionalArg($opt, $this->nativeToDatum($val));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DISTINCT;
    }
}
