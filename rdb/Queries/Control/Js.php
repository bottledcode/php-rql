<?php

namespace r\Queries\Control;

use r\Datum\NumberDatum;
use r\Datum\StringDatum;
use r\FunctionQuery\FunctionQuery;
use r\ProtocolBuffer\TermTermType;
use r\Query;

class Js extends FunctionQuery
{
    public function __construct(string|Query $code, int|null|float $timeout = null)
    {
        $this->setPositionalArg(0, ($code instanceof Query) ? $code : new StringDatum($code));
        null !== $timeout && $this->setOptionalArg('timeout', new NumberDatum($timeout));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_JAVASCRIPT;
    }
}
