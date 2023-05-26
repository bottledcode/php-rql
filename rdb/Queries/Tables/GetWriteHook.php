<?php

namespace r\Queries\Tables;

use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class GetWriteHook extends ValuedQuery
{
    public function __construct(Table $table)
    {
        $this->setPositionalArg(0, $table);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_GET_WRITE_HOOK;
    }
}
