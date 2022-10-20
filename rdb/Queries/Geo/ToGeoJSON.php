<?php

namespace r\Queries\Geo;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class ToGeoJSON extends ValuedQuery
{
    public function __construct($geometry)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($geometry));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_TO_GEOJSON;
    }
}
