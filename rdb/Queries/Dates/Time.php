<?php

namespace r\Queries\Dates;

use r\ProtocolBuffer\TermTermType;
use r\Query;
use r\ValuedQuery\ValuedQuery;

class Time extends ValuedQuery
{
    public function __construct(
        int|Query $year,
        int|Query $month,
        int|Query $day,
        string|int|null|Query $hourOrTimezone = null,
        string|int|null|Query $minute = null,
        string|int|null|Query $second = null,
        string|int|null|Query $timezone = null
    ) {
        $this->setPositionalArg(0, $this->nativeToDatum($year));
        $this->setPositionalArg(1, $this->nativeToDatum($month));
        $this->setPositionalArg(2, $this->nativeToDatum($day));
        $this->setPositionalArg(3, $this->nativeToDatum($hourOrTimezone));
        if (isset($minute)) {
            $this->setPositionalArg(4, $this->nativeToDatum($minute));
        }
        if (isset($second)) {
            $this->setPositionalArg(5, $this->nativeToDatum($second));
        }
        if (isset($timezone)) {
            $this->setPositionalArg(6, $this->nativeToDatum($timezone));
        }
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_TIME;
    }
}
