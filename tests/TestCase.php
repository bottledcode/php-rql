<?php

namespace r\Tests;

use r\ConnectionInterface;
use r\ConnectionOptions;

use function r\connect;
use function r\connectAsync;

class TestCase extends \PHPUnit\Framework\TestCase
{
	protected array $datasets = array();
	protected ConnectionInterface $conn;
	protected mixed $data;

	public function setUp(): void
	{
		$this->conn = $this->getConnection(getenv('ASYNC') === 'yes');
	}

	// return the current db connection
	protected function getConnection(bool $async = false)
	{
		if ($async) {
			return connectAsync(
				new ConnectionOptions(
					host: getenv('RDB_HOST') ?: 'localhost', port: getenv('RDB_PORT') ?: 28015, db: getenv(
						'RDB_DB'
					) ?: 'test'
				)
			);
		}
		return connect(
			new ConnectionOptions(host: getenv('RDB_HOST'), port: getenv('RDB_PORT'), db: getenv('RDB_DB'))
		);
	}

	// enable $this->db(), instead of \rdb('DB_NAME'), in tests
	protected function db()
	{
		return \r\db(getenv('RDB_DB'));
	}

	// returns the requested dataset
	protected function useDataset($name)
	{
		static $datasets;

		if (!isset($datasets[$name])) {
			$ds = 'r\Tests\Datasets\\' . $name;
			$datasets[$name] = new $ds($this->conn);
		}

		return $datasets[$name];
	}

	// test the results status
	protected function assertObStatus($status, $data)
	{
		$statuses = array(
			'unchanged',
			'skipped',
			'replaced',
			'inserted',
			'errors',
			'deleted'
		);

		foreach ($statuses as $s) {
			$status[$s] = $status[$s] ?? 0;
		}

		foreach ($statuses as $s) {
			$res[$s] = $data[$s] ?? 0;
		}

		$this->assertEquals($status, $res);
	}

	// convert a results objects (usually ArrayObject) to an array
	// works on multidimensional arrays, too
	protected function toArray($object)
	{
		return json_decode(json_encode($object), true);
	}
}
