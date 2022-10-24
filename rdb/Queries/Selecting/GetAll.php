<?php

namespace r\Queries\Selecting;

use r\Options\GetAllOptions;
use r\ProtocolBuffer\TermTermType;
use r\Queries\Tables\Table;
use r\ValuedQuery\ValuedQuery;

class GetAll extends ValuedQuery
{
    public function __construct(Table $table, mixed $key, GetAllOptions $opts = new GetAllOptions())
    {
        $key = $this->nativeToDatum($key);

        $this->setPositionalArg(0, $table);
        $this->setPositionalArg(1, $key);
        foreach ($opts as $k => $v) {
            if ($v === null) {
                continue;
            }
            $this->setOptionalArg($k, $this->nativeToDatum($v));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_GET_ALL;
    }
}
