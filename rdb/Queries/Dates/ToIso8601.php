<?php

namespace r\Queries\Dates;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class ToIso8601 extends ValuedQuery
{
    public function __construct(ValuedQuery $time)
    {
        $this->setPositionalArg(0, $time);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_TO_ISO8601;
    }
}
