<?php

namespace r\ValuedQuery;

use r\Exceptions\RqlDriverError;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class MakeObject extends ValuedQuery
{
    public function __construct(array $value)
    {
        foreach ($value as $key => $val) {
            $this->setOptionalArg($key, $val);
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_MAKE_OBJ;
    }
}
