<?php

namespace r\Queries\Control;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class CoerceTo extends ValuedQuery
{
    public function __construct(ValuedQuery $value, string|Query $typeName)
    {
        $this->setPositionalArg(0, $value);
        $this->setPositionalArg(1, $this->nativeToDatum($typeName));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_COERCE_TO;
    }
}
