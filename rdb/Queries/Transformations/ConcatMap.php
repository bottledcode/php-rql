<?php

namespace r\Queries\Transformations;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class ConcatMap extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, callable|Query $mappingFunction)
    {
        $mappingFunction = $this->nativeToFunction($mappingFunction);

        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $mappingFunction);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_CONCAT_MAP;
    }
}
