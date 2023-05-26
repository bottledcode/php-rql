<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;

class BitSal extends BinaryOp
{
	public function __construct($value, $other)
	{
		parent::__construct(TermTermType::PB_BIT_SAL, $value, $other);
	}
}
