<?php

namespace r\Datum;

use r\Datum\Datum;
use r\Exceptions\RqlDriverError;

class NullDatum extends Datum
{
    public function encodeServerRequest(): bool|null
    {
        return null;
    }

    public static function decodeServerResponse(mixed $json): NullDatum
    {
        $result = new NullDatum();
        $result->setValue(null);
        return $result;
    }

    public function setValue(array|object|string|null|float|bool|int $val): void
    {
        if (!is_null($val)) {
            throw new RqlDriverError("Not null: " . $val);
        }
        parent::setValue($val);
    }

    public function __toString(): string
    {
        return "null";
    }
}
