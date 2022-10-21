<?php

namespace r\Queries\Transformations;

use r\Ordering\Ordering;
use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class OrderBy extends ValuedQuery
{
    public function __construct(ValuedQuery $sequence, string|callable|object|array ...$keys)
    {
        // Check keys and convert strings
        if (isset($keys['index'])) {
            $this->setOptionalArg('index', $this->nativeToDatum($keys['index']));
            unset($keys['index']);
        }
        $keys = array_values($keys);
        $this->setPositionalArg(0, $sequence);
        foreach ($keys as $idx => $val) {
            if (!($val instanceof Ordering)) {
                $this->setPositionalArg($idx + 1, $this->nativeToDatumOrFunction($val));
            } else {
                $this->setPositionalArg($idx + 1, $val);
            }
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_ORDER_BY;
    }
}
