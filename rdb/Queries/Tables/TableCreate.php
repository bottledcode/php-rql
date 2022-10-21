<?php

namespace r\Queries\Tables;

use r\Options\TableCreateOptions;
use r\ProtocolBuffer\TermTermType;
use r\Queries\Dbs\Db;
use r\ValuedQuery\ValuedQuery;

class TableCreate extends ValuedQuery
{
    public function __construct(
        Db|null $database,
        string $tableName,
        TableCreateOptions $options = new TableCreateOptions()
    ) {
        $tableName = $this->nativeToDatum($tableName);

        $i = 0;
        $database !== null && $this->setPositionalArg($i++, $database);
        $this->setPositionalArg($i++, $tableName);

        $options->primaryKey !== null && $this->setOptionalArg(
            'primary_key',
            $this->nativeToDatum($options->primaryKey)
        );
        $options->durability !== null && $this->setOptionalArg(
            'durability',
            $this->nativeToDatum($options->durability->value)
        );
        $options->shards !== null && $this->setOptionalArg('shards', $this->nativeToDatum($options->shards));
        $options->replicas !== null && $this->setOptionalArg('replicas', $this->nativeToDatum($options->replicas));
        $options->primaryReplicaTag !== null && $this->setOptionalArg(
            'primary_replica_tag',
            $this->nativeToDatum($options->primaryReplicaTag)
        );
    }

    protected function getTermType(): TermTermType
    {
        return TermTermType::PB_TABLE_CREATE;
    }
}
