<?php

namespace r\Queries\Tables;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Rebalance extends ValuedQuery
{
    public function __construct(Query $tables)
    {
        $this->setPositionalArg(0, $tables);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_REBALANCE;
    }
}
