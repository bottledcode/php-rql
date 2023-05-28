<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class BinaryOp extends ValuedQuery
{
    private $termType;

    public function __construct($termType, $value, $other)
    {
        $this->termType = $termType;

        $this->setPositionalArg(0, $this->nativeToDatum($value));
        if (is_array($other)) {
            foreach ($other as $idx => $next) {
                $this->setPositionalArg($idx + 1, $this->nativeToDatum($next));
            }
        } else {
            $this->setPositionalArg(1, $this->nativeToDatum($other));
        }
    }

    protected function getTermType(): TermTermType
    {
        return $this->termType;
    }
}
