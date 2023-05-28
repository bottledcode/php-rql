<?php

namespace r\Tests\Datasets;

use r\Connection;

abstract class Dataset
{
    abstract public function create();
    abstract public function populate();
    abstract public function truncate();

    protected Connection $conn;

    protected string $db;

    private $mustDelete = true;

    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
        $this->db = $this->conn->defaultDbName;
    }
}
