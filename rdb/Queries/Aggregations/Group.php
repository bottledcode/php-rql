<?php

namespace r\Queries\Aggregations;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class Group extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, array $groupOn)
    {
        if (isset($groupOn['index'])) {
            $this->setOptionalArg('index', $this->nativeToDatum($groupOn['index']));
            unset($groupOn['index']);
        }

        $this->setPositionalArg(0, $sequence);
        $i = 1;
        foreach ($groupOn as $g) {
            $this->setPositionalArg($i++, $this->nativeToDatumOrFunction($g));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_GROUP;
    }
}
