<?php

namespace r\Queries\Geo;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Fill extends ValuedQuery
{
    public function __construct($g1)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($g1));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_FILL;
    }
}
