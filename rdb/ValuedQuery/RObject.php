<?php

namespace r\ValuedQuery;

use r\Exceptions\RqlDriverError;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class RObject extends ValuedQuery
{
    public function __construct(mixed ...$object)
    {
        $i = 0;
        foreach ($object as $v) {
            $this->setPositionalArg($i++, $this->nativeToDatum($v));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_OBJECT;
    }
}
