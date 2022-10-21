<?php

namespace r\Queries\Math;

use r\Options\RandomOptions;
use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Random extends ValuedQuery
{
    public function __construct(int|float|Query|null $left = null, int|float|Query|RandomOptions|null $right = null, RandomOptions|null $opts = null)
    {
        $opts = $right instanceof RandomOptions ? $right : $opts;
        $right = $right instanceof RandomOptions ? null : $right;

        if (isset($left)) {
            $this->setPositionalArg(0, $this->nativeToDatum($left));
        }
        if (isset($right)) {
            $this->setPositionalArg(1, $this->nativeToDatum($right));
        }
        if (isset($opts)) {
            foreach ($opts as $opt => $val) {
                $this->setOptionalArg($opt, $this->nativeToDatum($val));
            }
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_RANDOM;
    }
}
