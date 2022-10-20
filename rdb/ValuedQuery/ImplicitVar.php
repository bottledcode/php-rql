<?php

namespace r\ValuedQuery;

use r\ProtocolBuffer\TermTermType;

class ImplicitVar extends ValuedQuery
{
    public function hasUnwrappedImplicitVar(): bool
    {
        // A function wraps implicit variables
        return true;
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_IMPLICIT_VAR;
    }
}
