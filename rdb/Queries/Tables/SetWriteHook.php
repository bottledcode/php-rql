<?php

namespace r\Queries\Tables;

use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class SetWriteHook extends ValuedQuery
{
    public function __construct(Table $table, $writeHookFunction)
    {
        $writeHookFunction = $this->nativeToDatumOrFunction($writeHookFunction);
        $this->setPositionalArg(0, $table);
        $this->setPositionalArg(1, $writeHookFunction);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_SET_WRITE_HOOK;
    }
}
