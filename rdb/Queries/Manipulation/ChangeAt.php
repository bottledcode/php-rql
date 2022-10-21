<?php

namespace r\Queries\Manipulation;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class ChangeAt extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, int|Query $index, mixed $value)
    {
        $index = $this->nativeToDatum($index);
        $value = $this->nativeToDatum($value);

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $index);
        $this->setPositionalArg(2, $value);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_CHANGE_AT;
    }
}
