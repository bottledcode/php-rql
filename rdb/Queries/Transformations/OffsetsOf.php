<?php

namespace r\Queries\Transformations;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class OffsetsOf extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, mixed $predicate)
    {
        $predicate = $this->nativeToDatumOrFunction($predicate);

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $predicate);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_OFFSETS_OF;
    }
}
