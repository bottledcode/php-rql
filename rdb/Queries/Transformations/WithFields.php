<?php

namespace r\Queries\Transformations;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class WithFields extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, ...$attributes)
    {
        $attributes = $this->nativeToDatum($attributes);

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $attributes);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_WITH_FIELDS;
    }
}
