<?php

namespace r\Queries\Geo;

use r\Query;
use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class GeoJSON extends ValuedQuery
{
    public function __construct(array|object $geojson)
    {
        $this->setPositionalArg(0, $this->nativeToDatum($geojson));
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_GEOJSON;
    }
}
