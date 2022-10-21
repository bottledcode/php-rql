<?php

namespace r\Options;

use r\Exceptions\RqlDriverError;

class TableCreateOptions
{
    public function __construct(
        public readonly string|null $primaryKey = null,
        public readonly Durability|null $durability = null,
        public readonly int|null $shards = null,
        public readonly int|array|null $replicas = null,
        public readonly string|null $primaryReplicaTag = null,
    ) {
        if ($shards > 64) {
            throw new RqlDriverError("Shards must be <= 64");
        }
        if (is_array($replicas) && !array_key_exists($primaryReplicaTag, $replicas)) {
            throw new RqlDriverError("Primary replica tag must be a key in the replicas array");
        }
    }
}