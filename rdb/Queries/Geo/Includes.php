<?php

namespace r\Queries\Geo;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Includes extends ValuedQuery
{
    public function __construct($g1, $g2)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($g1));
        $this->setPositionalArg(1, $this->nativeToDatum($g2));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_INCLUDES;
    }
}
