<?php

namespace r\Tests\Functional;

use r\Options\RunOptions;
use r\Tests\TestCase;

// use function \r\expr;

class ProfilingTest extends TestCase
{
    public function testProfile()
    {
        \r\expr(1)->run($this->conn, new RunOptions(profile: true), $res);

        $this->assertEquals('Evaluating datum.', $res[0]['description']);

    }

    public function testProfileNoOpts()
    {
        $status = \r\expr(1)->run($this->conn, new RunOptions(profile: true), profile: $res);

        $this->assertEquals('Evaluating datum.', $res[0]['description']);
        $this->assertEquals(1, $status);

    }
}
