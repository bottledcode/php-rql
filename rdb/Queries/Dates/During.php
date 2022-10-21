<?php

namespace r\Queries\Dates;

use r\Options\SliceOptions;
use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class During extends ValuedQuery
{
    public function __construct(ValuedQuery $time, Query $startTime, Query $endTime, SliceOptions $opts)
    {
        $startTime = $this->nativeToDatum($startTime);
        $endTime = $this->nativeToDatum($endTime);

        $this->setPositionalArg(0, $time);
        $this->setPositionalArg(1, $startTime);
        $this->setPositionalArg(2, $endTime);
        foreach ($opts as $k => $v) {
            if ($v === null) {
                continue;
            }
            $this->setOptionalArg($k, $this->nativeToDatum($v));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DURING;
    }
}
