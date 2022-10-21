<?php

namespace r\Queries\Manipulation;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Without extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, array|object|callable|string ...$attributes)
    {
        // See comment above about pluck. The same applies here.
        $attributes = $this->nativeToDatum($attributes);

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $attributes);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_WITHOUT;
    }
}
