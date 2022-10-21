<?php

namespace r\Queries\Manipulation;

use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Merge extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, object|callable|array ...$other)
    {
        $this->setPositionalArg(0, $sequence);
        foreach ($other as $i => $value) {
            $this->setPositionalArg($i + 1, $this->nativeToDatumOrFunction($value));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_MERGE;
    }
}
