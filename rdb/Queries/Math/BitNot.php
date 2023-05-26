<?php

namespace r\Queries\Math;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class BitNot extends ValuedQuery
{
	public function __construct($value)
	{
		$this->setPositionalArg(0, $this->nativeToDatum($value));
	}

	protected function getTermType(): TermTermType
	{
		return TermTermType::PB_BIT_NOT;
	}
}
