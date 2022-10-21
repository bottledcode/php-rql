<?php

namespace r\Ordering;

use r\ProtocolBuffer\TermTermType;

class Desc extends Ordering
{
    public function __construct(callable|string $attribute)
    {
        $attribute = $this->nativeToDatumOrFunction($attribute);
        $this->setPositionalArg(0, $attribute);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DESC;
    }
}
