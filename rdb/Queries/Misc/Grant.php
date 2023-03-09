<?php

namespace r\Queries\Misc;

use r\Datum\BoolDatum;
use r\Datum\StringDatum;
use r\Options\GrantOptions;
use r\Options\GrantPermission;
use r\ProtocolBuffer\TermTermType;
use r\Queries\Dbs\Db;
use r\Queries\Tables\Table;
use r\ValuedQuery\ValuedQuery;

class Grant extends ValuedQuery
{
	public function __construct(Db|Table|null $scope, string $user, ...$permission)
	{
		$this->setPositionalArg(0, $scope);
		$this->setPositionalArg(1, new StringDatum($user));
		$this->setOptionalArg('permission', $this->nativeToDatum($permission));
	}

	protected function getTermType(): TermTermType
	{
		return TermTermType::PB_GRANT;
	}
}
