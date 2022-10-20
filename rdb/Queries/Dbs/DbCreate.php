<?php

namespace r\Queries\Dbs;

use r\Datum\StringDatum;
use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class DbCreate extends ValuedQuery
{
    public function __construct(string $dbName)
    {
        $this->setPositionalArg(0, new StringDatum($dbName));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DB_CREATE;
    }
}
