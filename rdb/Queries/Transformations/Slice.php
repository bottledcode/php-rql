<?php

namespace r\Queries\Transformations;

use r\Datum\NumberDatum;
use r\Datum\StringDatum;
use r\Options\SliceOptions;
use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Slice extends ValuedQuery
{
    public function __construct(
        ValuedQuery $sequence,
        int|Query $startIndex,
        int|Query|null $endIndex = null,
        SliceOptions $opts = new SliceOptions()
    ) {
        $this->setPositionalArg(0, $sequence);
        $this->setPositionalArg(1, $this->nativeToDatum($startIndex));
        if (isset($endIndex)) {
            $this->setPositionalArg(2, $this->nativeToDatum($endIndex));
        } else {
            $this->setPositionalArg(2, new NumberDatum(-1));
            $this->setOptionalArg('right_bound', new StringDatum('closed'));
        }
        foreach ($opts as $k => $v) {
            if ($v !== null) {
                $this->setOptionalArg($k, $this->nativeToDatum($v));
            }
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_SLICE;
    }
}
