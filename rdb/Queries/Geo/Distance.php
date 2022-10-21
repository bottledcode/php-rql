<?php

namespace r\Queries\Geo;

use r\Options\DistanceOptions;
use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Distance extends ValuedQuery
{
    public function __construct(Query $g1, Query $g2, DistanceOptions $opts = null)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($g1));
        $this->setPositionalArg(1, $this->nativeToDatum($g2));
        foreach ($opts as $k => $v) {
            if ($v === null) {
                continue;
            }
            $this->setOptionalArg($k, $this->nativeToDatum($v));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DISTANCE;
    }
}
