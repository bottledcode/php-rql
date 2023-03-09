<?php

namespace r\Options;

enum GrantPermission
{
	/**
	 * allows reading the data in tables.
	 */
	case Read;

	/**
	 * allows modifying data, including inserting, replacing/updating, and deleting.
	 */
	case Write;

	/**
	 *  allows a user to open HTTP connections via the http command. This permission can only be granted in global scope.
	 */
	case Connect;

	/**
	 * allows users to create/drop secondary indexes on a table and changing the cluster configuration; to create and drop tables, if granted on a database; and to create and drop databases, if granted globally.
	 */
	case Config;
}
