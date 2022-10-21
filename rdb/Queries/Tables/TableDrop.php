<?php

namespace r\Queries\Tables;

use r\ProtocolBuffer\TermTermType;
use r\Queries\Dbs\Db;
use r\ValuedQuery\ValuedQuery;

class TableDrop extends ValuedQuery
{
    public function __construct(Db|null $database, string $tableName)
    {
        $tableName = $this->nativeToDatum($tableName);

        $i = 0;
        $database !== null && $this->setPositionalArg($i++, $database);
        $this->setPositionalArg($i++, $tableName);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_TABLE_DROP;
    }
}
