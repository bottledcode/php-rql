<?php

namespace r\Queries\Aggregations;

use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Min extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, array $attributeOrOpts)
    {
        $this->setPositionalArg(0, $sequence);
        $i = 1;
        foreach ($attributeOrOpts as $key => $value) {
            if (is_string($key)) {
                $this->setOptionalArg($key, $this->nativeToDatum($value));
            } else {
                $this->setPositionalArg($i++, $this->nativeToDatumOrFunction($value));
            }
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_MIN;
    }
}
