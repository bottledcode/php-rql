<?php

namespace r\Queries\Dbs;

use r\Exceptions\RqlDriverError;
use r\Options\TableCreateOptions;
use r\Options\TableOptions;
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

    /**
     * Return all documents in a table. Other commands may be chained after table to return a subset of documents (such as
     * get and filter) or perform further processing.
     * @see https://rethinkdb.com/api/javascript/table/
     * @param string $tableName The name of the table to read from.
     * @param TableOptions $options
     * @return Table
     * @throws RqlDriverError
     */
    public function table(string $tableName, TableOptions $options = new TableOptions()): Table
    {
        return new Table($this, $tableName, $options);
    }

    /**
     * Create a table. A RethinkDB table is a collection of JSON documents.
     *
     * If successful, the command returns an object with two fields:
     *
     * - tables_created: always 1.
     * - config_changes: a list containing one two-field object, old_val and new_val:
     *   - old_val: always null.
     *   - new_val: the table’s new config value.
     * If a table with the same name already exists, the command throws ReqlOpFailedError.
     *
     * @param string $tableName The table name to create
     * @param TableCreateOptions $options
     * @return TableCreate
     */
    public function tableCreate(string $tableName, TableCreateOptions $options = new TableCreateOptions()): TableCreate
    {
        return new TableCreate($this, $tableName, $options);
    }

    /**
     * Drop a table from a database. The table and all its data will be deleted.
     *
     * If successful, the command returns an object with two fields:
     *
     * - tables_dropped: always 1.
     * - config_changes: a list containing one two-field object, old_val and new_val:
     *   - old_val: the dropped table’s config value.
     *   - new_val: always null.
     * If the given table does not exist in the database, the command throws ReqlRuntimeError.
     *
     * @param string $tableName The table to drop
     * @return TableDrop
     */
    public function tableDrop(string $tableName): TableDrop
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
