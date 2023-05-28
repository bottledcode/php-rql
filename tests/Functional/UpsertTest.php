<?php

namespace r\Tests\Functional;

use r\Options\TableInsertOptions;
use r\Tests\TestCase;

class UpsertTest extends TestCase
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

    public function testUpsertUnchanged()
    {
        $res = $this->db()->table('marvel')->insert(
            [
                'superhero' => 'Iron Man',
                'superpower' => 'Arc Reactor',
                'combatPower' => 2.0,
                'compassionPower' => 1.5
            ],
            new TableInsertOptions(conflict: 'update')
        )->run($this->conn);

        $this->assertObStatus(array('unchanged' => 1), $res);
    }

    public function testUpsertReplaced()
    {
        $res = $this->db()->table('marvel')->insert(
            [
                'superhero' => 'Iron Man',
                'superpower' => 'Suit'
            ],
            new TableInsertOptions(conflict: 'update')
        )->run($this->conn);

        $this->assertObStatus(array('replaced' => 1), $res);
    }

    public function testUpsertInserted()
    {
        $res = $this->db()->table('marvel')->insert(
            [
                'superhero' => 'Pepper',
                'superpower' => 'Stark Industries'
            ],
            new TableInsertOptions(conflict: 'update')
        )->run($this->conn);

        $this->assertObStatus(array('inserted' => 1), $res);
    }
}
