<?php

namespace r\Tests\Functional;

use r\Options\Durability;
use r\Options\RunOptions;
use r\Tests\TestCase;

// use function \r\row;
// use function \r\js;

class UpdateTest extends TestCase
{
    protected RunOptions $opts;

    public function setUp(): void
    {
        $this->conn = $this->getConnection();
        $this->data = $this->useDataset('Heroes');
        $this->data->populate();
        $this->opts = new RunOptions();
    }

    public function tearDown(): void
    {
        $this->data->truncate();
    }

    public function testUpdateUpdate()
    {
        $res = $this->db()->table('marvel')->get('Wolverine')->update(
            array('age' => 30)
        )->run($this->conn);

        $this->assertObStatus(array('replaced' => 1), $res);
    }

    public function testUpdateViaMethod()
    {
        $res = $this->db()->table('marvel')->update(
            function ($r) {
                return $r->merge(array('age' => 5));
            }
        )->run($this->conn, $this->opts);

        $this->assertObStatus(array('replaced' => 3), $res);
    }

    public function testUpdateRow()
    {
        $this->db()->table('marvel')->update(
            array('age' => 30)
        )->run($this->conn, $this->opts);

        $res = $this->db()->table('marvel')->update(
            array('age' => \r\row('age')->add(1))
        )->run($this->conn, $this->opts);

        $this->assertObStatus(array('replaced' => 3), $res);
    }

    public function testUpdateRowAdd()
    {
        $this->db()->table('marvel')->update(
            array('age' => 30)
        )->run($this->conn, $this->opts);

        $res = $this->db()->table('marvel')->update(
            array('age' => \r\row('age')->add(\r\js('1')))
        )->run($this->conn, new RunOptions(durability: Durability::Soft, non_atomic: true));

        $this->assertObStatus(array('replaced' => 3), $res);
    }
}
