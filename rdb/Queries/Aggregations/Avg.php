<?php

namespace r\Queries\Aggregations;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Avg extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, callable|null|string $attribute = null)
    {
        $this->setPositionalArg(0, $sequence);
        if (isset($attribute)) {
            $this->setPositionalArg(1, $this->nativeToDatumOrFunction($attribute));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_AVG;
    }
}
