<?php

namespace r\Datum;

use r\Datum\Datum;
use r\Exceptions\RqlDriverError;

class NumberDatum extends Datum
{
    public function encodeServerRequest(): float|int
    {
        return $this->getValue();
    }

    public static function decodeServerResponse(float|int $json): NumberDatum
    {
        $result = new NumberDatum();
        $result->setValue($json);
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
