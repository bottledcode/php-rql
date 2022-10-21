<?php

namespace r\Queries\Geo;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Point extends ValuedQuery
{
    public function __construct(int|float|Query $lat, int|float|Query $lon)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($lat));
        $this->setPositionalArg(1, $this->nativeToDatum($lon));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_POINT;
    }
}
