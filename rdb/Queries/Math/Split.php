<?php

namespace r\Queries\Math;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Split extends ValuedQuery
{
    public function __construct(ValuedQuery $value, string|Query|null $separator = null, int|Query|null $maxSplits = null)
    {
        $this->setPositionalArg(0, $value);
        if (isset($separator) || isset($maxSplits)) {
            $this->setPositionalArg(1, $this->nativeToDatum($separator));
        }
        if (isset($maxSplits)) {
            $this->setPositionalArg(2, $this->nativeToDatum($maxSplits));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_SPLIT;
    }
}
