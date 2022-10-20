<?php

namespace r\Queries\Dates;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class August extends ValuedQuery
{
    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_AUGUST;
    }
}
