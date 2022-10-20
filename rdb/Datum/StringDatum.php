<?php

namespace r\Datum;

use r\Datum\Datum;
use r\Exceptions\RqlDriverError;

class StringDatum extends Datum
{
    public function encodeServerRequest(): string
    {
        return (string)$this->getValue();
    }

    public static function decodeServerResponse($json): StringDatum
    {
        $result = new StringDatum();
        $result->setValue((string)$json);
        return $result;
    }

    public function setValue(array|object|string|null|float|bool|int $val): void
    {
        if (!is_string($val)) {
            throw new RqlDriverError("Not a string");
        }
        parent::setValue($val);
    }

    public function __toString(): string
    {
        return "'" . $this->getValue() . "'";
    }
}
