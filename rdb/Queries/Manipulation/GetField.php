<?php

namespace r\Queries\Manipulation;

use r\Datum\StringDatum;
use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class GetField extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, string|Query $attribute)
    {
        if (!$attribute instanceof Query) {
            $attribute = new StringDatum($attribute);
        }

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $attribute);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_GET_FIELD;
    }
}
