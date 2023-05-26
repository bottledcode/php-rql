<?php

namespace r\Queries\Tables;

use r\Datum\StringDatum;
use r\Exceptions\RqlDriverError;
use r\Options\GetAllOptions;
use r\Options\GrantOptions;
use r\Options\TableInsertOptions;
use r\Options\TableOptions;
use r\ProtocolBuffer\TermTermType;
use r\Queries\Dbs\Db;
use r\Queries\Geo\GetIntersecting;
use r\Queries\Geo\GetNearest;
use r\Queries\Index\IndexCreate;
use r\Queries\Index\IndexDrop;
use r\Queries\Index\IndexList;
use r\Queries\Index\IndexStatus;
use r\Queries\Index\IndexWait;
use r\Queries\Misc\Grant;
use r\Queries\Selecting\Get;
use r\Queries\Selecting\GetAll;
use r\Queries\Selecting\GetMultiple;
use r\Queries\Writing\Insert;
use r\Queries\Writing\Sync;
use r\ValuedQuery\ValuedQuery;

class Table extends ValuedQuery
{
    public function __construct(Db|null $database, string $tableName, TableOptions $options = new TableOptions())
    {
        $tableName = $this->nativeToDatum($tableName);

        $i = 0;
        if (isset($database)) {
            $this->setPositionalArg($i++, $database);
        }
        $this->setPositionalArg($i++, $tableName);
        $options->readMode !== null && $this->setOptionalArg('read_mode', new StringDatum($options->readMode->value));
        $options->identifierFormat !== null && $this->setOptionalArg(
            'identifier_format',
            new StringDatum($options->identifierFormat->value)
        );
    }

    public function insert(array|object $document, TableInsertOptions $opts = new TableInsertOptions()): Insert
    {
        return new Insert($this, $document, $opts);
    }

    public function grant(string $user, ...$permissions): Grant
    {
        return new Grant($this, $user, ...$permissions);
    }

    public function get($key): Get
    {
        return new Get($this, $key);
    }

    public function getAll(mixed $key, GetAllOptions $opts = new GetAllOptions()): GetAll
    {
        return new GetAll($this, $key, $opts);
    }

    public function getMultiple($keys, $opts = null): GetMultiple
    {
        return new GetMultiple($this, $keys, $opts);
    }

    public function setWriteHook(callable $writeHookFunction): SetWriteHook
    {
        return new SetWriteHook($this, $writeHookFunction);
    }

    public function getWriteHook(): GetWriteHook
    {
        return new GetWriteHook($this);
    }

    public function getIntersecting($geo, $opts = null): GetIntersecting
    {
        return new GetIntersecting($this, $geo, $opts);
    }

    public function getNearest($center, $opts = null): GetNearest
    {
        return new GetNearest($this, $center, $opts);
    }

    public function sync(): Sync
    {
        return new Sync($this);
    }

    public function indexCreate($indexName, $keyFunction = null): IndexCreate
    {
        return new IndexCreate($this, $indexName, $keyFunction);
    }

    public function indexCreateMulti($indexName, $keyFunction = null): IndexCreate
    {
        return new IndexCreate($this, $indexName, $keyFunction, array('multi' => true));
    }

    public function indexCreateGeo($indexName, $keyFunction = null): IndexCreate
    {
        return new IndexCreate($this, $indexName, $keyFunction, array('geo' => true));
    }

    public function indexCreateMultiGeo($indexName, $keyFunction = null): IndexCreate
    {
        return new IndexCreate($this, $indexName, $keyFunction, array('multi' => true, 'geo' => true));
    }

    public function indexDrop($indexName): IndexDrop
    {
        return new IndexDrop($this, $indexName);
    }

    public function indexList(): IndexList
    {
        return new IndexList($this);
    }

    public function indexStatus($indexNames = null): IndexStatus
    {
        return new IndexStatus($this, $indexNames);
    }

    public function indexWait($indexNames = null): IndexWait
    {
        return new IndexWait($this, $indexNames);
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

    public function config(): Config
    {
        return new Config($this);
    }

    public function status(): Status
    {
        return new Status($this);
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_TABLE;
    }
}
