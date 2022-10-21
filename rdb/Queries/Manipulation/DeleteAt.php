<?php

namespace r\Queries\Manipulation;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class DeleteAt extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, int|Query $index, int|Query|null $endIndex = null)
    {
        $index = $this->nativeToDatum($index);
        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $index);
        if (isset($endIndex)) {
            $this->setPositionalArg(2, $this->nativeToDatum($endIndex));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DELETE_AT;
    }
}
