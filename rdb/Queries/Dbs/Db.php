<?php

namespace r\Queries\Dbs;

use r\ProtocolBuffer\TermTermType;
use r\Queries\Tables\Rebalance;
use r\Queries\Tables\Reconfigure;
use r\Queries\Tables\Table;
use r\Queries\Tables\TableCreate;
use r\Queries\Tables\TableDrop;
use r\Queries\Tables\TableList;
use r\Queries\Tables\Wait;
use r\Query;

class Db extends Query
{
    public function __construct($dbName)
    {
        $dbName = $this->nativeToDatum($dbName);
        $this->setPositionalArg(0, $dbName);
    }

    public function table($tableName, $useOutdatedOrOpts = null): Table
    {
        return new Table($this, $tableName, $useOutdatedOrOpts);
    }

    public function tableCreate($tableName, $options = null): TableCreate
    {
        return new TableCreate($this, $tableName, $options);
    }

    public function tableDrop($tableName): TableDrop
    {
        return new TableDrop($this, $tableName);
    }

    public function tableList(): TableList
    {
        return new TableList($this);
    }

    public function wait($opts = null): Wait
    {
        return new Wait($this, $opts);
    }

    public function reconfigure($opts = null): Reconfigure
    {
        return new Reconfigure($this, $opts);
    }

    public function rebalance(): Rebalance
    {
        return new Rebalance($this);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_DB;
    }
}
