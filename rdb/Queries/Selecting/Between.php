<?php

namespace r\Queries\Selecting;

use r\Options\BetweenOptions;
use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Between extends ValuedQuery
{
    public function __construct(ValuedQuery $selection, mixed $leftBound, mixed $rightBound, BetweenOptions $opts)
    {
        $leftBound = $this->nativeToDatum($leftBound);
        $rightBound = $this->nativeToDatum($rightBound);

        $this->setPositionalArg(0, $selection);
        $this->setPositionalArg(1, $leftBound);
        $this->setPositionalArg(2, $rightBound);
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
        return TermTermType::PB_BETWEEN;
    }
}
