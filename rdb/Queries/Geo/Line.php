<?php

namespace r\Queries\Geo;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Line extends ValuedQuery
{
    public function __construct(array|Query ...$points)
    {
        $i = 0;
        foreach ($points as $point) {
            $this->setPositionalArg($i++, $point instanceof Query ? $point : $this->nativeToDatum($point));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_LINE;
    }
}
