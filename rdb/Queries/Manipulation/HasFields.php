<?php

namespace r\Queries\Manipulation;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class HasFields extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, string|Query ...$attributes)
    {
        $this->setPositionalArg(0, $sequence);
        foreach ($attributes as $i => $attribute) {
            $this->setPositionalArg($i + 1, $this->nativeToDatum($attribute));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_HAS_FIELDS;
    }
}
