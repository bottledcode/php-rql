<?php

namespace r\Queries\Transformations;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class MapMultiple extends ValuedQuery
{
    public function __construct(array|Query $sequence, callable|Query|array ...$mappingFunction)
    {
        $last = end($mappingFunction);

        $this->setPositionalArg(0, $sequence instanceof Query ? $sequence : $this->nativeToDatum($sequence));
        for ($i = 0; $i < count($mappingFunction) - 1; $i++) {
            $this->setPositionalArg(
                $i + 1,
                $mappingFunction[$i] instanceof Query
                    ? $mappingFunction[$i]
                    : $this->nativeToDatum($mappingFunction[$i])
            );
        }
        $this->setPositionalArg($i + 1, $last instanceof Query ? $last : $this->nativeToFunction($last));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_MAP;
    }
}
