<?php

namespace r\Queries\Selecting;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Filter extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, callable|Query|array $predicate, mixed $default = null)
    {
        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $this->nativeToDatumOrFunction($predicate));
        if (isset($default)) {
            $this->setOptionalArg('default', $this->nativeToDatum($default));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_FILTER;
    }
}
