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
        if (isset($timeout)) {
            $timeout = new NumberDatum($timeout);
        }
        if (!(is_object($code) && is_subclass_of($code, Query::class))) {
            $code = new StringDatum($code);
        }

        $this->setPositionalArg(0, $code);
        if (isset($timeout)) {
            $this->setOptionalArg('timeout', $timeout);
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_JAVASCRIPT;
    }
}
