<?php

namespace r\Queries\Manipulation;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Prepend extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, mixed $value)
    {
        $value = $this->nativeToDatum($value);

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $value);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_PREPEND;
    }
}
