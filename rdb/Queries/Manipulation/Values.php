<?php

namespace r\Queries\Manipulation;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Values extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence)
    {
        $this->setPositionalArg(0, $sequence);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_VALUES;
    }
}
