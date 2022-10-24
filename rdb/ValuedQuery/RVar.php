<?php

namespace r\ValuedQuery;

use r\Exceptions\RqlDriverError;
use r\ValuedQuery\ValuedQuery;
use r\Datum\NumberDatum;
use r\ProtocolBuffer\TermTermType;

class RVar extends ValuedQuery
{

    private int $id;
    public string $name;

    private static int $nextVarId = 1;

    public function __construct(string $name)
    {
        $this->id = RVar::$nextVarId;
        $this->name = $name;

        if (RVar::$nextVarId == (1 << 31) - 1) {
            RVar::$nextVarId = 0; // TODO: This is not ideal. In very very very rare cases, it could lead to collisions.
        } else {
            ++RVar::$nextVarId;
        }

        $this->setPositionalArg(0, new NumberDatum($this->id));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_VAR;
    }

    public function getId()
    {
        return $this->id;
    }
}
