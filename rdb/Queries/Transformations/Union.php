<?php

namespace r\Queries\Transformations;

use r\Options\UnionOptions;
use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Union extends ValuedQuery
{
    public function __construct(array|Query $sequence, array|Query|UnionOptions ...$otherSequence)
    {
        $this->setPositionalArg(0, $sequence instanceof Query ? $sequence : $this->nativeToDatum($sequence));

        $options = null;

        for ($i = 0; $i < count($otherSequence); $i++) {
            if ($otherSequence[$i] instanceof UnionOptions) {
                $options = $otherSequence[$i];
                continue;
            }
            $this->setPositionalArg(
                $i + 1,
                $otherSequence[$i] instanceof Query ? $otherSequence[$i] : $this->nativeToDatum($otherSequence[$i])
            );
        }

        $options?->interleave !== null && $this->setOptionalArg(
            'interleave',
            $this->nativeToDatumOrFunction($options->interleave)
        );
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_UNION;
    }
}
