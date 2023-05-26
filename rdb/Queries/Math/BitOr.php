<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;

class BitOr extends BinaryOp
{
	public function __construct($value, $other)
	{
		parent::__construct(TermTermType::PB_BIT_OR, $value, $other);
	}
}
