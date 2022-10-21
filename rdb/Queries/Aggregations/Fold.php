<?php

namespace r\Queries\Aggregations;

use r\Options\FoldOptions;
use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Fold extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, mixed $base, callable|Query $fun, FoldOptions $opts)
    {
        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $this->nativeToDatum($base));
        $this->setPositionalArg(2, $this->nativeToFunction($fun));

        foreach ($opts as $opt => $val) {
            if ($val === null) {
                continue;
            }
            $this->setOptionalArg($opt, $this->nativeToDatumOrFunction($val));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_FOLD;
    }
}
