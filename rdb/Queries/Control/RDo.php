<?php

namespace r\Queries\Control;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class RDo extends ValuedQuery
{
    public function __construct(array $args, Query|callable $inExpr)
    {
        $inExpr = $this->nativeToFunction($inExpr);
        $this->setPositionalArg(0, $inExpr);

        $i = 1;
        foreach ($args as $arg) {
            if (!(is_object($arg) && is_subclass_of($arg, Query::class))) {
                $arg = $this->nativeToDatum($arg);
            }
            $this->setPositionalArg($i++, $arg);
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_FUNCALL;
    }
}
