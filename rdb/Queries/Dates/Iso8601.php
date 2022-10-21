<?php

namespace r\Queries\Dates;

use r\Options\Iso8601Options;
use r\ProtocolBuffer\TermTermType;
use r\ValuedQuery\ValuedQuery;

class Iso8601 extends ValuedQuery
{
    public function __construct(string $iso8601Date, Iso8601Options $opts = new Iso8601Options())
    {
        $iso8601Date = $this->nativeToDatum($iso8601Date);

        $this->setPositionalArg(0, $iso8601Date);
        $opts->defaultTimezone !== null && $this->setOptionalArg(
            'default_timezone',
            $this->nativeToDatum($opts->defaultTimezone)
        );
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_ISO8601;
    }
}
