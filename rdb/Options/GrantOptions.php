<?php

namespace r\Options;

readonly class GrantOptions
{
	public array $permissions;

	public function __construct(GrantPermission ...$permissions)
	{
		$this->permissions = array_combine(
			array_flip(
				array_map(static fn(GrantPermission $p) => match ($p) {
					GrantPermission::Config => 'config',
					GrantPermission::Connect => 'connect',
					GrantPermission::Read => 'read',
					GrantPermission::Write => 'write',
				}, $permissions)
			),
			array_fill(0, count($permissions), true)
		);
	}
}
