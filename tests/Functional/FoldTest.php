<?php

namespace r\Tests\Functional;

use r\Options\FoldOptions;
use r\Tests\TestCase;

class FoldTest extends TestCase
{
    public function testFoldReduction()
    {
        $this->assertEquals(
            15.0,
            \r\expr(array(1, 2, 3, 4))
                ->fold(5, function ($acc, $v) {
                    return $acc->add($v);
                })
                ->run($this->conn)
        );
    }

    public function testFoldEmit()
    {
        $this->assertEquals(
            array(5, 6, 8, 11, 15),
            \r\expr(array(1, 2, 3, 4))
                ->fold(
                    5,
                    function ($acc, $v) {
                        return $acc->add($v);
                    },
                    new FoldOptions(emit: fn($o, $c, $n) => [$o], final_emit: fn($a) => [$a])
                )
                ->run($this->conn)
        );
    }
}
