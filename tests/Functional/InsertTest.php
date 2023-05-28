<?php

namespace r\Tests\Functional;

use r\Options\TableInsertOptions;
use r\Tests\TestCase;

use function r\expr;

class InsertTest extends TestCase
{
    public function setUp(): void
    {
        $this->conn = $this->getConnection();
        $this->data = $this->useDataset('Heroes');
        $this->data->populate();
    }

    public function tearDown(): void
    {
        $this->data->truncate();
    }

    public function testCustomConflict()
    {
        $res = $this->db()->table('marvel')->insert(
            array(
                'superhero' => 'Iron Man',
            ),
            new TableInsertOptions(conflict: static fn($x, $k, $o) => expr(null))
        )->run($this->conn);

        $this->assertObStatus(array('deleted' => 1), $res);
    }
}
