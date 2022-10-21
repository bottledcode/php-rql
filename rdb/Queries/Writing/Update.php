<?php

namespace r\Queries\Writing;

use r\Exceptions\RqlDriverError;
use r\Options\UpdateOptions;
use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\Json;
use r\ValuedQuery\ValuedQuery;

class Update extends ValuedQuery
{
    public function __construct(Query $selection, array|object|callable $delta, UpdateOptions $opts)
    {
        if (!($delta instanceof Query)) {
            try {
                $json = $this->tryEncodeAsJson($delta);
                if ($json !== false) {
                    $delta = new Json($json);
                } else {
                    $delta = $this->nativeToDatum($delta);
                }
            } catch (RqlDriverError $e) {
                $delta = $this->nativeToFunction($delta);
            }
        }
        $delta = $this->wrapImplicitVar($delta);

        $this->setPositionalArg(0, $selection);
        $this->setPositionalArg(1, $delta);
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
        return TermTermType::PB_UPDATE;
    }
}
