<?php

namespace r\Queries\Joins;

use r\Options\EqJoinOptions;
use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class EqJoin extends ValuedQuery
{
    public function __construct(
        ValuedQuery $sequence,
        string|callable|Query $leftFieldOrFunction,
        ValuedQuery $otherSequence,
        EqJoinOptions $opts = new EqJoinOptions()
    ) {
        $attribute = $this->nativeToDatumOrFunction($leftFieldOrFunction);
        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $attribute);
        $this->setPositionalArg(2, $otherSequence);

        foreach ($opts as $k => $v) {
            if ($v === null) {
                continue;
            }
            if ($v instanceof \BackedEnum) {
                $v = $v->value;
            }
            $this->setOptionalArg($k, $this->nativeToDatum($v));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_EQ_JOIN;
    }
}
