<?php

namespace r\Queries\Dates;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Wednesday extends ValuedQuery
{
    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_WEDNESDAY;
    }
}
