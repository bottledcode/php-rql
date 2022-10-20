<?php

namespace r\ValuedQuery;

use r\ProtocolBuffer\TermTermType;
use r\Query;

class Literal extends ValuedQuery
{
    public function __construct(...$args)
    {
        if (count($args) > 0) {
            $value = $args[0];
            if (!(is_object($value) && is_subclass_of($value, Query::class))) {
                $value = $this->nativeToDatum($value);
            }
            $this->setPositionalArg(0, $value);
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_LITERAL;
    }
}
