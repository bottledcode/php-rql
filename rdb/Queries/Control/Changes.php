<?php

namespace r\Queries\Control;

use r\Options\ChangesOptions;
use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Changes extends ValuedQuery
{
    public function __construct(ValuedQuery $src, ChangesOptions $opts)
    {
        $this->setPositionalArg(0, $src);
        foreach ($opts as $opt => $val) {
            if ($val === null) {
                continue;
            }
            $this->setOptionalArg($opt, $this->nativeToDatum($val));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_CHANGES;
    }
}
