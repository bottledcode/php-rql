<?php

namespace r\Queries\Geo;

use r\Options\CircleOptions;
use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Circle extends ValuedQuery
{
    public function __construct(array|Query $center, int|float|Query $radius, CircleOptions $opts)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($center));
        $this->setPositionalArg(1, $this->nativeToDatum($radius));
        foreach ($opts as $k => $v) {
            if ($v === null) {
                continue;
            }
            $this->setOptionalArg($k, $this->nativeToDatum($v));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_CIRCLE;
    }
}
