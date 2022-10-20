<?php

namespace r\Datum;

use r\Datum\Datum;
use r\Exceptions\RqlDriverError;

class NumberDatum extends Datum
{
    public function encodeServerRequest(): float
    {
        return (float)$this->getValue();
    }

    public static function decodeServerResponse(mixed $json): NumberDatum
    {
        $result = new NumberDatum();
        $result->setValue((float)$json);
        return $result;
    }

    public function setValue(array|object|string|int|float|null|bool $val): void
    {
        if (!is_numeric($val)) {
            throw new RqlDriverError("Not a number: " . $val);
        }
        parent::setValue($val);
    }
}
