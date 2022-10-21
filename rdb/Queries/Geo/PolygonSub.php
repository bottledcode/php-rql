<?php

namespace r\Queries\Geo;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class PolygonSub extends ValuedQuery
{
    public function __construct(Query $p1, Query $p2)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($p1));
        $this->setPositionalArg(1, $this->nativeToDatum($p2));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_POLYGON_SUB;
    }
}
