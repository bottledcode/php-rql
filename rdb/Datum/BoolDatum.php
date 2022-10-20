<?php

namespace r\Datum;

use r\Datum\Datum;
use r\Exceptions\RqlDriverError;

class BoolDatum extends Datum
{
    public function encodeServerRequest(): bool
    {
        return (bool)$this->getValue();
    }

    public static function decodeServerResponse(mixed $json): BoolDatum
    {
        $result = new BoolDatum();
        $result->setValue((bool)$json);
        return $result;
    }

    public function __toString(): string
    {
        return $this->getValue() ? 'true' : 'false';
    }

    public function setValue(array|string|object|int|float|bool|null $val): void
    {
        if (is_numeric($val)) {
            $val = !($val === 0);
        }
        if (!is_bool($val)) {
            throw new RqlDriverError("Not a boolean: " . $val);
        }
        parent::setValue($val);
    }
}
