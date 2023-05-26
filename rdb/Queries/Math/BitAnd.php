<?php

namespace r\Queries\Math;

use r\ValuedQuery\ValuedQuery;
use r\ProtocolBuffer\TermTermType;

class BitAnd extends BinaryOp
{
	public function __construct($value, $other)
	{
		parent::__construct(TermTermType::PB_BIT_AND, $value, $other);
	}
}
