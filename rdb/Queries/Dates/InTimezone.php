<?php

namespace r\Queries\Dates;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class InTimezone extends ValuedQuery
{
    public function __construct(ValuedQuery $time, string|Query $timezone)
    {
        $timezone = $this->nativeToDatum($timezone);

        $this->setPositionalArg(0, $time);
        $this->setPositionalArg(1, $timezone);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_IN_TIMEZONE;
    }
}
