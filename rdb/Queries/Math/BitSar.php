<?php

namespace r\Queries\Math;

use r\ProtocolBuffer\TermTermType;

class BitSar extends BinaryOp
{
	public function __construct($value, $other)
	{
		parent::__construct(TermTermType::PB_BIT_SAR, $value, $other);
	}
}
