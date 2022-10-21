<?php

namespace r\Queries\Aggregations;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Contains extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, string|int|float|callable|Query ...$values)
    {
        $this->setPositionalArg(0, $sequence);
        foreach ($values as $i => $value) {
            $this->setPositionalArg($i + 1, $this->nativeToDatumOrFunction($value));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_CONTAINS;
    }
}
