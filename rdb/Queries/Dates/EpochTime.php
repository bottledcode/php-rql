<?php

namespace r\Queries\Dates;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class EpochTime extends ValuedQuery
{
    public function __construct(int|float|Query $epochTime)
    {
        $epochTime = $this->nativeToDatum($epochTime);

        $this->setPositionalArg(0, $epochTime);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_EPOCH_TIME;
    }
}
