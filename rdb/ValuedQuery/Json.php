<?php

namespace r\ValuedQuery;

use r\Datum\StringDatum;
use r\ProtocolBuffer\TermTermType;
use r\Query;

class Json extends ValuedQuery
{
    public function __construct(string|Query $json)
    {
        $this->setPositionalArg(0, $json instanceof Query ? $json : new StringDatum($json));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_JSON;
    }
}
