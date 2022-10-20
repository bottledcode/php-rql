<?php

namespace r\ValuedQuery;

use r\Exceptions\RqlDriverError;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class MakeArray extends ValuedQuery
{
    public function __construct(array $value)
    {
        $i = 0;
        foreach ($value as $val) {
            $this->setPositionalArg($i++, $val);
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_MAKE_ARRAY;
    }
}
