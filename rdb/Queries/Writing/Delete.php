<?php

namespace r\Queries\Writing;

use r\Options\DeleteOptions;
use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Delete extends ValuedQuery
{
    public function __construct(ValuedQuery $selection, DeleteOptions $opts)
    {
        $this->setPositionalArg(0, $selection);

        foreach ($opts as $opt => $val) {
            if ($val === null) {
                continue;
            }
            if ($val instanceof \BackedEnum) {
                $val = $val->value;
            }
            $this->setOptionalArg($opt, $this->nativeToDatum($val));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DELETE;
    }
}
