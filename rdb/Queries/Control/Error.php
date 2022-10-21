<?php

namespace r\Queries\Control;

use r\Datum\StringDatum;
use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Error extends ValuedQuery
{
    public function __construct(string|null $message = null)
    {
        if (null !== $message) {
            $message = new StringDatum($message);
            $this->setPositionalArg(0, $message);
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_ERROR;
    }
}
