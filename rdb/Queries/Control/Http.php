<?php

namespace r\Queries\Control;

use r\Options\HttpOptions;
use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Http extends ValuedQuery
{
    public function __construct(string $url, HttpOptions $opts = new HttpOptions())
    {
        $this->setPositionalArg(0, $this->nativeToDatum($url));
        foreach ($opts as $opt => $val) {
            if ($val === null) {
                continue;
            }
            $this->setOptionalArg($opt, $this->nativeToDatum($val));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_HTTP;
    }
}
