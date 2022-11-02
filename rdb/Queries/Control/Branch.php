<?php

namespace r\Queries\Control;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Branch extends ValuedQuery
{
    public function __construct(Query $test, mixed ...$branches)
    {
        $this->setPositionalArg(0, $test);

        if (!array_is_list($branches)) {
            $branches = array_values($branches);
        }

        foreach ($branches as $i => $branch) {
            $this->setPositionalArg($i + 1, $this->nativeToDatumOrFunction($branch, false));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_BRANCH;
    }
}
