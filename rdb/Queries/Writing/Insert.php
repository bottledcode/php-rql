<?php

namespace r\Queries\Writing;

use r\Options\TableInsertOptions;
use r\ProtocolBuffer\TermTermType;
use r\Queries\Tables\Table;
use r\Query;
use r\ValuedQuery\Json;
use r\ValuedQuery\ValuedQuery;

class Insert extends ValuedQuery
{
    public function __construct(
        Table $table,
        object|array $document,
        TableInsertOptions $opts = new TableInsertOptions()
    ) {
        if ($document instanceof Query) {
            $json = $this->tryEncodeAsJson($document);
            if ($json !== false) {
                $document = new Json($json);
            } else {
                $document = $this->nativeToDatum($document);
            }
        }

        $this->setPositionalArg(0, $table);
        $this->setPositionalArg(1, $document);
        foreach ($opts as $opt => $val) {
            if ($val === null) {
                continue;
            }
            $this->setOptionalArg($opt, $this->nativeToDatumOrFunction($val));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_INSERT;
    }
}
